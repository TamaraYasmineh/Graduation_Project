<?php
namespace App\Services;

use App\Models\PsychologicalSupport;
use Illuminate\Support\Facades\Storage;

class PsychologicalSupportService
{
    // إضافة
    public function store($data, $user)
    {
        if (!$user->can('create_support')) {
            return [
                'success' => false,
                'message' => 'Only super doctor can add support',
                'code' => 403
            ];
        }

        // رفع الصورة
        $imagePath = $data['image']->store('supports', 'public');

        $support = PsychologicalSupport::create([
            'title' => $data['title'],
            'content' => $data['content'],
            'image' => $imagePath,
            'created_by' => $user->id,
        ]);

        return [
            'success' => true,
            'data' => $support
        ];
    }


    public function update($id, $data, $user)
    {
        $support = PsychologicalSupport::find($id);

        if (!$support) {
            return [
                'success' => false,
                'message' => 'Support not found',
                'code' => 404
            ];
        }

        if (!$user->can('update_support') &&
        $user->id !== $support->created_by)
            return [
                'success' => false,
                'message' => 'Unauthorized',
                'code' => 403
            ];
        

        // تعديل الصورة إذا أُرسلت
        if (isset($data['image'])) {
            // حذف القديمة
            if ($support->image) {
                Storage::disk('public')->delete($support->image);
            }

            $data['image'] = $data['image']->store('supports', 'public');
        }

        $support->update($data);

        return [
            'success' => true,
            'data' => $support
        ];
    }


    public function delete($id, $user)
    {
        $support = PsychologicalSupport::find($id);

        if (!$support) {
            return [
                'success' => false,
                'message' => 'Support not found',
                'code' => 404
            ];
        }

        if (!$user->can('delete_support') &&
            $user->id !== $support->created_by) {
            return [
                'success' => false,
                'message' => 'Unauthorized',
                'code' => 403
            ];
        }

        // حذف الصورة
        if ($support->image) {
            Storage::disk('public')->delete($support->image);
        }

        $support->delete();

        return [
            'success' => true,
            'message' => 'Deleted successfully'
        ];
    }


    public function show()
    {
        return PsychologicalSupport::with('creator')->latest()->get();
    }
}
