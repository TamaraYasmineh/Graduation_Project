<?php
namespace App\Services;

use App\Models\CenterInfo;

class CenterInfoService
{
    public function storeCenterInfo($data, $user)
    {
        if (!$user->can('manage_center')) {
            return [
                'success' => false,
                'message' => 'Only super doctor can add center info',
                'code' => 403
            ];
        }

        $center = CenterInfo::create($data);

        return [
            'success' => true,
            'data' => $center
        ];
    }

    public function updateCenterInfo($id, $data, $user)
    {
        if (!$user->can('manage_center')) {
            return [
                'success' => false,
                'message' => 'Only super doctor can update',
                'code' => 403
            ];
        }

        $center = CenterInfo::find($id);

        if (!$center) {
            return [
                'success' => false,
                'message' => 'Center info not found',
                'code' => 404
            ];
        }

        $center->update($data);

        return [
            'success' => true,
            'data' => $center
        ];
    }
public function getAllCenters()
{
     $centers = CenterInfo::latest()->get();

    return [
        'success' => true,
        'data' => $centers
    ];
}

}
