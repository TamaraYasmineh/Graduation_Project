<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Advice
            'create_advice',
            'update_advice',
            'delete_advice',
            'view_advice',
    
            // Support
            'create_support',
            'update_support',
            'delete_support',
            'view_support',
    
            // Center
            'manage_center',
    
            // Users
            'approve_users',
            'reject_users',
            'view_doctors',
            'toggle_doctor_role',

            'create_schedule',
            'create_medical_record',
        ];
    
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    
        // Roles
        $superDoctor = Role::findByName('super_doctor', 'api');
        $doctor = Role::findByName('doctor', 'api');
        $patient = Role::findByName('patient', 'api');
    
        // Super Doctor → كل شي
        $superDoctor->givePermissionTo($permissions);
    
        // Doctor
        $doctor->givePermissionTo([
            'view_advice',
            'view_support',
            'create_schedule',
        ]);
    
        // Patient
        $patient->givePermissionTo([
            'view_advice',
            'view_support',
            'view_doctors',
            'create_medical_record',
        ]);
    }
}
