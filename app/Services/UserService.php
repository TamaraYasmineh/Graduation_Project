<?php
namespace App\Services;

use App\Models\User;
class UserService {

public function getPendingUsers($user)
{
    if ($user->role !== 'super_doctor') {
        return [
            'success' => false,
            'message' => 'Unauthorized',
            'code' => 403
        ];
    }

    $users = User::role(['doctor', 'secretary'])
    ->where('status', 'pending')
    ->latest()
    ->get();

    return [
        'success' => true,
        'data' => $users
    ];
}

//approveUser
public function approveUser($id, $user)
{
    if (!$user->hasRole('super_doctor')) {
        return [
            'success' => false,
            'message' => 'Unauthorized',
            'code' => 403
        ];
    }

    $targetUser = User::find($id);

    if (!$targetUser) {
        return [
            'success' => false,
            'message' => 'User not found',
            'code' => 404
        ];
    }

    //  تحقق من الدور
    if (!$targetUser->hasAnyRole(['doctor', 'secretary'])) {
        return [
            'success' => false,
            'message' => 'You can only approve doctors or secretaries',
            'code' => 403
        ];
    }

    $targetUser->update([
        'status' => User::STATUS_APPROVED
    ]);

    return [
        'success' => true,
        'message' => 'User approved successfully'
    ];
}

//rejectUser
public function rejectUser($id, $user)
{
    if (!$user->hasRole('super_doctor')) {
        return [
            'success' => false,
            'message' => 'Unauthorized',
            'code' => 403
        ];
    }

    $targetUser = User::find($id);

    if (!$targetUser) {
        return [
            'success' => false,
            'message' => 'User not found',
            'code' => 404
        ];
    }

    //  تحقق من الدور
    if (!$targetUser->hasAnyRole(['doctor', 'secretary'])) {
        return [
            'success' => false,
            'message' => 'You can only reject doctors or secretaries',
            'code' => 403
        ];
    }

    $targetUser->update([
        'status' => User::STATUS_REJECTED
    ]);

    return [
        'success' => true,
        'message' => 'User rejected successfully'
    ];
}

}
