<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Car;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CategoryCarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', \App\Constants\UserRole::ADMIN)->first();
        if (!$admin) {
            $admin = User::factory()->create([
                'name' => 'Ahmed',
                'email' => 'Ahmed@gmail.com',
                'phone' => '77019481',
                'password' => Hash::make('ahmed0073'),
                'role' => \App\Constants\UserRole::ADMIN,
            ]);
        }

        $categories = [
            [
                'name' => 'سيارات عائلية',
                'description' => 'سيارات مريحة وواسعة تناسب جميع أفراد العائلة.',
                'image' => json_encode(['categories/family_cars.png']),
                'cars' => [
                    ['name' => 'تويوتا لاند كروزر', 'description' => 'سيارة دفع رباعي فاخرة وقوية.'],
                    ['name' => 'هوندا أوديسي', 'description' => 'مينيفان عائلية بامتياز.'],
                    ['name' => 'نيسان باترول', 'description' => 'بطل الدروب والرحلات العائلية.'],
                    ['name' => 'كيا تيلورايد', 'description' => 'سيارة عائلية عصرية بـ 3 صفوف مقاعد.'],
                ]
            ],
            [
                'name' => 'شاحنات',
                'description' => 'شاحنات قوية للأعمال الشاقة والنقل.',
                'image' => json_encode(['categories/trucks.png']),
                'cars' => [
                    ['name' => 'فورد F-150', 'description' => 'الشاحنة الأكثر مبيعاً وقوة.'],
                    ['name' => 'تويوتا هيلوكس', 'description' => 'الاعتمادية والقوة في شاحنة واحدة.'],
                    ['name' => 'مرسيدس بنز أكتروس', 'description' => 'شاحنة نقل ثقيل متطورة.'],
                    ['name' => 'إيسوزو دي ماكس', 'description' => 'شاحنة بيك آب عملية واقتصادية.'],
                ]
            ],
            [
                'name' => 'حافلات',
                'description' => 'حافلات لنقل الركاب والمجموعات الكبيرة.',
                'image' => json_encode(['categories/buses.png']),
                'cars' => [
                    ['name' => 'تويوتا كوستر', 'description' => 'حافلة ركاب متوسطة مشهورة.'],
                    ['name' => 'مرسيدس بنز سبرينتر', 'description' => 'حافلة فان فاخرة للمجموعات.'],
                    ['name' => 'حافلة مدرسية', 'description' => 'حافلة آمنة لنقل الطلاب.'],
                    ['name' => 'حافلة سياحية', 'description' => 'حافلة كبيرة مجهزة للرحلات الطويلة.'],
                ]
            ],
            [
                'name' => 'دراجات نارية',
                'description' => 'دراجات نارية سريعة وممتعة للقيادة.',
                'image' => json_encode(['categories/motorcycles.png']),
                'cars' => [
                    ['name' => 'هارلي ديفيدسون', 'description' => 'دراجة كلاسيكية ذات طابع خاص.'],
                    ['name' => 'هوندا CBR', 'description' => 'دراجة رياضية سريعة جداً.'],
                    ['name' => 'ياماها R1', 'description' => 'قوة الأداء والتصميم الرياضي.'],
                    ['name' => 'كاواساكي نينجا', 'description' => 'السرعة والرشاقة على الطريق.'],
                ]
            ],
        ];

        foreach ($categories as $categoryData) {
            $carsData = $categoryData['cars'];
            unset($categoryData['cars']);
            
            $categoryData['image'] = null;

            $category = Category::create($categoryData);

            foreach ($carsData as $carData) {
                Car::create([
                    'name' => $carData['name'],
                    'description' => $carData['description'],
                    'image' => null,
                    'model' => date('Y'),
                    'latitude' => 24.7136,
                    'longitude' => 46.6753,
                    'price_per_day' => rand(100, 500),
                    'status' => \App\Constants\CarStatus::AVAILABLE,
                    'category_id' => $category->id,
                    'user_id' => $admin->id,
                    'stars_count' => rand(10, 150), // عدد النجوم العشوائي
                    'rating' => rand(3, 5) + (rand(0, 9) / 10), // تقييم بين 3.0 و 5.0
                ]);
            }
        }
    }
}
