<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * إضافة مستخدمين تجريبيين: مدرسين وطلاب.
     */
    public function run(): void
    {
        // تعريف بيانات المستخدمين التجريبيين.
        $users = [
            [
                'name' => 'أحمد المدرس',
                'email' => 'teacher1@darsak.test',
                'role' => 'teacher',
            ],
            [
                'name' => 'سارة المدرسة',
                'email' => 'teacher2@darsak.test',
                'role' => 'teacher',
            ],
            [
                'name' => 'محمد الطالب',
                'email' => 'student1@darsak.test',
                'role' => 'student',
            ],
            [
                'name' => 'نور الطالبة',
                'email' => 'student2@darsak.test',
                'role' => 'student',
            ],
        ];

        // المرور على بيانات المستخدمين وإنشاء حساب لكل مستخدم.
        foreach ($users as $userData) {

            // البحث بالبريد الإلكتروني، وإنشاء الحساب فقط إذا لم يكن موجوداً.
            User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'role' => $userData['role'],

                    // تشفير كلمة المرور قبل تخزينها في قاعدة البيانات.
                    'password' => Hash::make('Password123!'),
                ]
            );
        }
    }
}
