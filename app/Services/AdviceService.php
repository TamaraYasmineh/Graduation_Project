<?php

namespace App\Services;

use App\Models\Advice;

class AdviceService
{
    public function store($data, $user)
    {
        if ($user->role !== 'super_doctor') {
            return [
                'success' => false,
                'message' => 'Only super admin can add advice',
                'code' => 403
            ];
        }

        $advice = Advice::create([
            'title' => $data['title'],
            'content' => $data['content'],
            'created_by' => $user->id,
        ]);

        return [
            'success' => true,
            'data' => $advice
        ];
    }

    public function getAdvices($user)
    {
        if (!in_array($user->role, ['patient', 'super_doctor'])) {
            return [
                'success' => false,
                'message' => 'Unauthorized',
                'code' => 403
            ];
        }

        $advices = Advice::with('creator')->latest()->paginate(10);

        return [
            'success' => true,
            'data' => $advices
        ];
    }
}
