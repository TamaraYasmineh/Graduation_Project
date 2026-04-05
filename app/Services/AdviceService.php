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

        $advices = Advice::with('creator')->latest()->get();

        return [
            'success' => true,
            'data' => $advices
        ];
    }
    public function deleteAdvice($id, $user)
    {
        $advice = Advice::find($id);

        if (!$advice) {
            return [
                'success' => false,
                'message' => 'Advice not found',
                'code' => 404
            ];
        }

        if ($user->role !== 'super_doctor' && $user->id !== $advice->created_by) {
            return [
                'success' => false,
                'message' => 'Unauthorized',
                'code' => 403
            ];
        }

        $advice->delete();

        return [
            'success' => true,
            'message' => 'Advice deleted successfully'
        ];
    }
}
