<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create a default Department
        $dept = Department::create([
            'name_tk' => 'IT Bölümi',
            'name_ru' => 'IT Отдел',
            'name_en' => 'IT Department',
        ]);

        // 2. Create the Admin User
        $admin = User::create([
            'full_name' => 'System Administrator',
            'username' => 'admin',
            'email' => 'admin@company.gov',
            'password' => Hash::make('admin123'),
            'department_id' => $dept->id,
            'role_level' => 'admin',
            'preferred_lang' => 'en',
            'is_active' => true,
        ]);

        // 3. Create Spatie Roles (Since you installed the package)
        $adminRole = Role::create(['name' => 'admin']);
        Role::create(['name' => 'manager']);
        Role::create(['name' => 'employee']);

        // 4. Assign Admin Role to the user
        $admin->assignRole($adminRole);

        $this->command->info('Admin user created successfully!');
        $this->command->info('Username: admin');
        $this->command->info('Password: admin123');
    }
}
