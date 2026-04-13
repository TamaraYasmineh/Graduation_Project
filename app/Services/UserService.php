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
    
        $users = User::with('roles')
            ->role(['doctor', 'secretary'])
            ->where('status', 'pending')
            ->latest()
            ->get();
        $users = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'gender' => $user->gender,
                'status' => $user->status,
                'role' => $user->roles->pluck('name')->first()
            ];
        });
    
        return [
            'success' => true,
            'data' => $users
        ];
    }

    public function getRejectedUsers($user)
{
    if (!$user->hasRole('super_doctor')) {
        return [
            'success' => false,
            'message' => 'Unauthorized',
            'code' => 403
        ];
    }

    $users = User::with('roles')
        ->role(['doctor', 'secretary'])
        ->where('status', User::STATUS_REJECTED)
        ->latest()
        ->get();

    $users = $users->map(function ($user) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'gender' => $user->gender,
            'status' => $user->status,
            'role' => $user->roles->pluck('name')->first()
        ];
    });

    return [
        'success' => true,
        'message' => 'Rejected users fetched successfully',
        'data' => $users
    ];
}
public function getApprovedUsers($user)
{
    if (!$user->hasRole('super_doctor')) {
        return [
            'success' => false,
            'message' => 'Unauthorized',
            'code' => 403
        ];
    }

    $users = User::with('roles')
        ->role(['doctor', 'secretary'])
        ->where('status', User::STATUS_APPROVED)
        ->latest()
        ->get();

    $users = $users->map(function ($user) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'gender' => $user->gender,
            'status' => $user->status,
            'role' => $user->roles->pluck('name')->first()
        ];
    });

    return [
        'success' => true,
        'message' => 'Approved users fetched successfully',
        'data' => $users
    ];
}
public function getSuperDoctors($user)
{
    if (!$user->hasRole('super_doctor')) {
        return [
            'success' => false,
            'message' => 'Unauthorized',
            'code' => 403
        ];
    }

    $users = User::with('roles')
        ->role('super_doctor')
        ->latest()
        ->get()
        ->map(fn($user) => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'gender' => $user->gender,
            'status' => $user->status,
            'role' => $user->roles->pluck('name')->first()
        ]);

    return [
        'success' => true,
        'message' => 'Super doctors fetched successfully',
        'data' => $users
    ];
}
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
