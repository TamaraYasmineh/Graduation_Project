<?php

/*namespace App\Http\Controllers;

use App\Models\DeviceToken;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FirebaseController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase->getDatabase();
    }

    public function test()
    {
        $this->firebase->getReference("testing")
            ->set([
                'message' => 'Firebase Integration Successful!'
            ]);

        return 'Firebase Connected and Test Data Added!';
    }

public function saveFcmToken($user, $token)
{
    if (!$token) return;
    DeviceToken::where('token', $token)->delete();
    DeviceToken::create([
        'user_id' => $user->id,
        'token' => $token
    ]);
}
}*/
