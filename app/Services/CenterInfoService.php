<?php

namespace App\Services;

use App\Models\CenterInfo;
use Illuminate\Support\Facades\DB;

class CenterInfoService
{
    public function storeCenterInfo($data, $user)
    {
        if (! $user->can('manage_center')) {
            return [
                'success' => false,
                'message' => 'Only super doctor can add center info',
                'code' => 403,
            ];
        }

        DB::beginTransaction();

        try {

            $workingHours = $data['working_hours'] ?? [];
            unset($data['working_hours']);

            $center = CenterInfo::create($data);

            if (! empty($workingHours)) {
                $center->workingHours()->createMany($workingHours);
            }

            DB::commit();

            return [
                'success' => true,
                'data' => $center->load('workingHours'),
            ];

        } catch (\Exception $e) {

            DB::rollBack();

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'code' => 500,
            ];
        }
    }

    public function updateCenterInfo($id, $data, $user)
    {
        if (! $user->can('manage_center')) {
            return [
                'success' => false,
                'message' => 'Only super doctor can update',
                'code' => 403,
            ];
        }

        $center = CenterInfo::find($id);

        if (! $center) {
            return [
                'success' => false,
                'message' => 'Center info not found',
                'code' => 404,
            ];
        }

        DB::beginTransaction();

        try {

            $workingHours = $data['working_hours'] ?? [];
            unset($data['working_hours']);

            $center->update($data);

            if (! empty($workingHours)) {

                $center->workingHours()->delete();

                $center->workingHours()->createMany($workingHours);
            }

            DB::commit();

            return [
                'success' => true,
                'data' => $center->load('workingHours'),
            ];

        } catch (\Exception $e) {

            DB::rollBack();

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'code' => 500,
            ];
        }
    }

    public function getAllCenters()
    {
        $centers = CenterInfo::with('workingHours')
            ->latest()
            ->get();

        return [
            'success' => true,
            'data' => $centers,
        ];
    }
}
