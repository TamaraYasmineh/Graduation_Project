<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Models\Doctor;
use App\Models\DoctorReview;
class DoctorReviewController extends BaseController
{
     // =========================
    // 1. إضافة تقييم
    // =========================
    public function addReview(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'rating'    => 'required|integer|min:1|max:5',
            'comment'   => 'nullable|string',
        ]);

        $patient = $request->user();

        // تحقق إذا قيّم المريض هذا الطبيب مسبقاً
        $exists = DoctorReview::query()->where('doctor_id', $request->doctor_id)
            ->where('patient_id', $patient->id)
            ->exists();

        if ($exists) {
            return $this->sendError('لقد قمت بتقييم هذا الطبيب مسبقاً', [], 400);
        }

        $review = DoctorReview::create([
            'doctor_id'  => $request->doctor_id,
            'patient_id' => $patient->id,
            'rating'     => $request->rating,
            'comment'    => $request->comment,
        ]);

        return $this->sendResponse($review, 'تم إضافة التقييم بنجاح');
    }

    // =========================
    // 2. تعديل تقييم
    // =========================
    public function updateReview(Request $request, $id)
    {
        $request->validate([
        'rating'  => 'sometimes|integer|min:1|max:5',
        'comment' => 'nullable|string',
    ]);

    $review = DoctorReview::query()->where('id', $id)->first();

    if (!$review) {
        return $this->sendError('التقييم غير موجود', [], 404);
    }

    $user = $request->user();

    //  فقط المريض صاحب التقييم أو super_doctor
    if ($review->patient_id !== $user->id && !$user->hasRole('super_doctor')) {
        return $this->sendError('غير مصرح لك بتعديل هذا التقييم', [], 403);
    }

    $review->update($request->only(['rating', 'comment']));

    return $this->sendResponse($review, 'تم تعديل التقييم بنجاح');
    }

    // =========================
    // 3. حذف تقييم
    // =========================
    public function deleteReview(Request $request, $id)
    {
     $review = DoctorReview::query()->where('id', $id)->first();

    if (!$review) {
        return $this->sendError('التقييم غير موجود', [], 404);
    }

    $user = $request->user();

    //  فقط المريض صاحب التقييم أو super_doctor
    if ($review->patient_id !== $user->id && !$user->hasRole('super_doctor')) {
        return $this->sendError('غير مصرح لك بحذف هذا التقييم', [], 403);
    }

    $review->delete();

    return $this->sendResponse(null, 'تم حذف التقييم بنجاح');
    }

    // =========================
    // 4. جلب تقييمات طبيب
    // =========================
    public function getDoctorReviews($doctorId)
    {
        $doctor = Doctor::find($doctorId);

        if (!$doctor) {
            return $this->sendError('الطبيب غير موجود', [], 404);
        }

       $reviews = DoctorReview::where('doctor_id', $doctorId)
    ->with('patient:id,name,profile_image')
    ->latest()
    ->get()
    ->map(function ($review) {
        $review->patient->profile_image = $review->patient->profile_image
            ? asset('storage/' . $review->patient->profile_image)
            : null;
        return $review;
    });

        return $this->sendResponse([
            'average_rating' => $doctor->calculateAverageRating(),
            'total_reviews'  => $reviews->count(),
            'reviews'        => $reviews,
        ], 'Doctor Reviews');
    }

}
