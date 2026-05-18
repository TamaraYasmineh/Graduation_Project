<?php

namespace App\Http\Controllers\SuperDoctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

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
}
