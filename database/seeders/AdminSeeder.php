<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'nouralkassar19@gmail.com'],
            [
                'name' => 'manal',
                'phone' => '0999999999',
                'password' => Hash::make('manal1111'),
                'gender' => 'female',
                'status' => User::STATUS_APPROVED,
            ]
        );

        $admin->assignRole('super_doctor');
        Doctor::firstOrCreate(
            ['user_id' => $admin->id],
            [
                'specialization' => 'cancer',
                'years_of_experience' => 15,
                'license_number' => 'SD111111',
                'bio' => 'Super Doctor Bio',
                'department' => 'blood',
            ]
        );
    }
}
