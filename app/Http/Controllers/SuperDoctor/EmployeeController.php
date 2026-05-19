<?php

namespace App\Http\Controllers\SuperDoctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    /**
     * POST /api/employees
     * إضافة موظف جديد — للسوبر أدمن فقط
     */
    public function storeEmployee(StoreEmployeeRequest $request): JsonResponse
    {
        DB::beginTransaction();

        $profileImagePath = null;
        $degreeImagePath  = null;

        try {
            $validated = $request->validated();

            // ------------------------------------------------
            // 1) رفع صورة الموظف الشخصية
            // ------------------------------------------------
            if ($request->hasFile('profile_image')) {
                $profileImagePath = $request->file('profile_image')
                    ->store('employees/profiles', 'public');
            }

            // ------------------------------------------------
            // 2) رفع صورة الشهادة (مطلوبة للممرض)
            // ------------------------------------------------
            if ($request->hasFile('degree_image')) {
                $degreeImagePath = $request->file('degree_image')
                    ->store('employees/degrees', 'public');
            }

            // ------------------------------------------------
            // 3) إنشاء User
            // ------------------------------------------------
            $user = User::create([
                'name'          => $validated['name'],
                'email'         => $validated['email'],
                'phone'         => $validated['phone'],
                'gender'        => $validated['gender'],
                'profile_image' => $profileImagePath,
                'password'      => Hash::make(Str::random(16)),
                'status'        => 'approved',
                'is_active'     => true,
            ]);

            // تعيين الدور عبر Spatie
            $user->assignRole($validated['role']);

            // ------------------------------------------------
            // 4) إنشاء Employee
            // ------------------------------------------------
            $employee = Employee::create([
                'user_id'          => $user->id,
                'role'             => $validated['role'],
                'date_of_birth' => $validated['date_of_birth'],
                'phone2'          => $validated['phone2']          ?? null,
                'academic_degree'  => $validated['academic_degree'],
                'degree_image'     => $degreeImagePath,
                'work_history'     => $validated['work_history']     ?? null,
                'chronic_diseases' => $validated['chronic_diseases'] ?? null,
                'marital_status'   => $validated['marital_status'],
                'bank_account'     => $validated['bank_account']     ?? null,
                'sham_cash_number' => $validated['sham_cash_number'] ?? null,
                'salary'           => $validated['salary'],
                'shift' => $validated['shift'],
                'work_days' => $validated['work_days'],
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم إضافة الموظف بنجاح.',
                'data'    => new EmployeeResource($employee->load('user')),
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            // حذف الصور إن رُفعت قبل الخطأ
            if ($profileImagePath) Storage::disk('public')->delete($profileImagePath);
            if ($degreeImagePath)  Storage::disk('public')->delete($degreeImagePath);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إضافة الموظف.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function getAllEmployees(): JsonResponse
    {
        $employees = Employee::with('user')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'count'   => $employees->count(),
            'data'    => EmployeeResource::collection($employees),
        ]);
    }

    public function getEmployeeById($id): JsonResponse
    {
        $employee = Employee::with('user')->find($id);

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'الموظف غير موجود.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new EmployeeResource($employee),
        ]);
    }

    public function updateEmployeeInfo(UpdateEmployeeRequest $request, Employee $employee): JsonResponse
    {
        DB::beginTransaction();

        try {

            $validated = $request->validated();

            // ---------------------------
            // تحديث بيانات user
            // ---------------------------
            $userData = [];

            if (isset($validated['name'])) {
                $userData['name'] = $validated['name'];
            }

            if (isset($validated['email'])) {
                $userData['email'] = $validated['email'];
            }

            if (isset($validated['phone'])) {
                $userData['phone'] = $validated['phone'];
            }

            if (isset($validated['gender'])) {
                $userData['gender'] = $validated['gender'];
            }

            // صورة جديدة
            if ($request->hasFile('profile_image')) {

                if ($employee->user->profile_image) {
                    Storage::disk('public')->delete($employee->user->profile_image);
                }

                $userData['profile_image'] = $request
                    ->file('profile_image')
                    ->store('employees/profiles', 'public');
            }

            $employee->user->update($userData);

            // ---------------------------
            // تحديث بيانات employee
            // ---------------------------
            $employeeData = collect($validated)->except([
                'name',
                'email',
                'phone',
                'gender',
                'profile_image'
            ])->toArray();

            // تحديث صورة الشهادة
            if ($request->hasFile('degree_image')) {

                if ($employee->degree_image) {
                    Storage::disk('public')->delete($employee->degree_image);
                }

                $employeeData['degree_image'] = $request
                    ->file('degree_image')
                    ->store('employees/degrees', 'public');
            }

            // تحديث role في Spatie
            if (isset($validated['role'])) {

                $employee->user->syncRoles([$validated['role']]);
            }

            $employee->update($employeeData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم تعديل بيانات الموظف بنجاح.',
                'data' => new EmployeeResource(
                    $employee->load('user')
                ),
            ]);
        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تعديل الموظف.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null,
            ], 500);
        }
    }

    public function deleteEmployee(Employee $employee): JsonResponse
    {
        DB::beginTransaction();

        try {

            // ---------------------------
            // حذف صورة البروفايل
            // ---------------------------
            if ($employee->user?->profile_image) {

                Storage::disk('public')
                    ->delete($employee->user->profile_image);
            }

            // ---------------------------
            // حذف صورة الشهادة
            // ---------------------------
            if ($employee->degree_image) {

                Storage::disk('public')
                    ->delete($employee->degree_image);
            }

            // ---------------------------
            // حذف المستخدم المرتبط
            // ---------------------------
            $employee->user()?->delete();

            // ---------------------------
            // حذف الموظف
            // ---------------------------
            $employee->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الموظف بنجاح.',
            ]);
        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف الموظف.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null,
            ], 500);
        }
    }
}
