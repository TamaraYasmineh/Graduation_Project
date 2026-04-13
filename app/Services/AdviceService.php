<?php

namespace App\Services;

use App\Models\Advice;
use Illuminate\Support\Facades\Storage;
class AdviceService
{
    public function store($data, $user)
    {
        if (!$user->can('create_advice')) {
            return [
                'success' => false,
                'message' => 'Only super admin can add advice',
                'code' => 403
            ];
        }
        $iconPath = null;

        if (isset($data['icon'])) {
            $iconPath = $data['icon']->store('advices/icons', 'public');
        }
        $advice = Advice::create([
            'title' => $data['title'],
            'content' => $data['content'],
            'icon' => $iconPath,
            'created_by' => $user->id,
        ]);

        return [
            'success' => true,
            'data' => $advice
        ];
    }

    public function getAdvices($user)
    {
        if (!$user->hasAnyRole(['patient', 'super_doctor'])) {
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

        if (!$user->can('delete_advice') &&
        $user->id !== $advice->created_by) {
            return [
                'success' => false,
                'message' => 'Unauthorized',
                'code' => 403
            ];
        }
        if ($advice->icon && Storage::disk('public')->exists($advice->icon)) {
            Storage::disk('public')->delete($advice->icon);
        }
        $advice->delete();

        return [
            'success' => true,
            'message' => 'Advice deleted successfully'
        ];
    }

    public function updateAdvice($id, $data, $user)
{
    $advice = Advice::find($id);

    if (!$advice) {
        return [
            'success' => false,
            'message' => 'Advice not found',
            'code' => 404
        ];
    }

   
    if (!$user->can('update_advice') &&
    $user->id !== $advice->created_by) {
        return [
            'success' => false,
            'message' => 'Unauthorized',
            'code' => 403
        ];
    }
    if (request()->hasFile('icon')) {

        
        if ($advice->icon && Storage::disk('public')->exists($advice->icon)) {
            Storage::disk('public')->delete($advice->icon);
        }

        
        $data['icon'] = request()->file('icon')->store('advices/icons', 'public');
    }
    $advice->update($data);

    return [
        'success' => true,
        'data' => $advice
    ];
}
}
