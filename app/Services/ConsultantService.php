<?php

namespace App\Services;

use App\Models\Consultant;
use App\Models\Doctor;
use App\Models\ExternalDoctor;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ConsultantService
{
    public function createInternalConsultant(
        array $data
    ): Consultant {

        $doctor = Doctor::findOrFail(
            $data['doctor_id']
        );

        if ($doctor->consultant) {

            throw new \Exception(
                'هذا الطبيب استشاري مسبقاً'
            );
        }

        return $doctor->consultant()->create([
            'consultation_fee' => $data['consultation_fee'],

            'whatsapp_number' => $data['whatsapp_number'],

            'is_active' => true,
        ]);
    }

    public function createExternalConsultant(
        array $data
    ): Consultant {

        $imagePath = null;

        if (
            isset($data['profile_image'])
            &&
            $data['profile_image'] instanceof UploadedFile
        ) {

            $imagePath = $data['profile_image']
                ->store(
                    'consultants',
                    'public'
                );
        }

        $externalDoctor =
            ExternalDoctor::create([

                'name' => $data['name'],

                'phone' => $data['phone'],

                'specialization' => $data['specialization'],

                'years_of_experience' => $data['years_of_experience']
                    ?? null,

                'license_number' => $data['license_number']
                    ?? null,

                'bio' => $data['bio']
                    ?? null,

                'profile_image' => $imagePath,
            ]);

        return $externalDoctor
            ->consultant()
            ->create([

                'consultation_fee' => $data['consultation_fee'],

                'whatsapp_number' => $data['whatsapp_number'],

                'is_active' => true,
            ]);
    }

    public function update(
        Consultant $consultant,
        array $data
    ): Consultant {

        // \Log::info($data);
        // die('CHECK LOG');
        // dd([
        //     'has_profile_image' => isset($data['profile_image']),
        //     'profile_image_class' => isset($data['profile_image'])
        //         ? get_class($data['profile_image'])
        //         : null,
        // ]);

        $consultable = $consultant->consultable;

        /*
        |-------------------------
        | صورة جديدة
        |-------------------------
        */

        if (isset($data['profile_image'])) {

            $path = $data['profile_image']
                ->store('consultants', 'public');

            if ($consultable instanceof Doctor) {

                if (
                    $consultable->user->profile_image
                ) {
                    Storage::disk('public')
                        ->delete(
                            $consultable->user->profile_image
                        );
                }

                $consultable->user->update([
                    'profile_image' => $path,
                ]);
                \Log::info([
                    'saved_path' => $path,
                    'db_value' => $consultable->user->fresh()->profile_image,
                ]);
            }

            if (
                $consultable instanceof ExternalDoctor
            ) {

                if ($consultable->profile_image) {

                    Storage::disk('public')
                        ->delete(
                            $consultable->profile_image
                        );
                }

                $consultable->update([
                    'profile_image' => $path,
                ]);
            }
        }

        /*
        |-------------------------
        | طبيب داخلي
        |-------------------------
        */

        if ($consultable instanceof Doctor) {

            $consultable->update([
                'specialization' => $data['specialization']
                    ?? $consultable->specialization,

                'bio' => $data['bio']
                    ?? $consultable->bio,

                'years_of_experience' => $data['years_of_experience']
                    ?? $consultable->years_of_experience,
            ]);

            $consultable->user->update([
                'name' => $data['name']
                    ?? $consultable->user->name,

                'phone' => $data['phone']
                    ?? $consultable->user->phone,
            ]);
        }

        /*
        |-------------------------
        | طبيب خارجي
        |-------------------------
        */

        if (
            $consultable instanceof ExternalDoctor
        ) {

            $consultable->update([

                'name' => $data['name']
                    ?? $consultable->name,

                'phone' => $data['phone']
                    ?? $consultable->phone,

                'specialization' => $data['specialization']
                    ?? $consultable->specialization,

                'bio' => $data['bio']
                    ?? $consultable->bio,

                'years_of_experience' => $data['years_of_experience']
                    ?? $consultable->years_of_experience,
            ]);
        }

        /*
        |-------------------------
        | بيانات الاستشاري
        |-------------------------
        */

        $consultant->update([

            'consultation_fee' => $data['consultation_fee']
                ?? $consultant->consultation_fee,

            'whatsapp_number' => $data['whatsapp_number']
                ?? $consultant->whatsapp_number,

            'is_active' => $data['is_active']
                ?? $consultant->is_active,
        ]);

        return $consultant->fresh();
    }

    public function findConsultantOrFail(
        int $id
    ): Consultant {
        $consultant = Consultant::query()->find($id);

        if (! $consultant) {

            throw new \Exception(
                'الاستشاري غير موجود'
            );
        }

        return $consultant;
    }
}
