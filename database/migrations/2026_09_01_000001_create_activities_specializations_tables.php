<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * V2 hierarchy: System -> Activity -> Specialization.
 *
 * "System" stays a fixed three-value list (orders | booking | service) because the client
 * explicitly ruled out a fourth system, so it lives in App\Helpers\Systems rather than a table.
 * Activities and specializations are data, so they get tables the admin can grow.
 *
 * Each activity also carries the legacy `business_type` and default `template` so every existing
 * storefront/theme code path keeps working without being rewritten.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('activities')) {
            Schema::create('activities', function (Blueprint $t) {
                $t->id();
                $t->string('system', 20)->index();          // orders | booking | service
                $t->string('name');
                $t->string('name_ar')->nullable();
                $t->string('slug')->index();
                $t->string('business_type', 30)->nullable(); // maps onto the legacy settings.business_type
                $t->unsignedTinyInteger('template')->nullable(); // default storefront template
                $t->unsignedInteger('reorder_id')->default(0);
                $t->tinyInteger('is_available')->default(1);
                $t->timestamps();
            });
        }

        if (!Schema::hasTable('specializations')) {
            Schema::create('specializations', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('activity_id')->index();
                $t->string('name');
                $t->string('name_ar')->nullable();
                $t->string('slug')->index();
                $t->unsignedInteger('reorder_id')->default(0);
                $t->tinyInteger('is_available')->default(1);
                $t->timestamps();
            });
        }

        if (DB::table('activities')->count() > 0) {
            return; // already seeded
        }

        // Starter catalogue. The client is sending the final list separately; this is a working
        // set that covers every example they gave, and the admin can edit it in place.
        $catalogue = [
            // system, activity, business_type, template, [specializations]
            ['orders', 'Restaurants & Cafés', 'مطاعم ومقاهي', 'food', 3, [
                'Restaurant' => 'مطعم', 'Café' => 'مقهى', 'Bakery' => 'مخبز', 'Fast Food' => 'وجبات سريعة',
                'Juice & Beverages' => 'عصائر ومشروبات', 'Sweets & Desserts' => 'حلويات',
            ]],
            ['orders', 'Grocery & Supermarkets', 'بقالة وسوبرماركت', 'grocery', 5, [
                'Supermarket' => 'سوبرماركت', 'Mini Market' => 'ميني ماركت', 'Fruits & Vegetables' => 'خضار وفواكه',
                'Butcher' => 'ملحمة', 'Dairy & Bakery Goods' => 'ألبان ومخبوزات',
            ]],
            ['orders', 'Pharmacies & Medical Supplies', 'صيدليات ومستلزمات طبية', 'pharmacy', 7, [
                'Pharmacy' => 'صيدلية', 'Drugstore' => 'متجر أدوية', 'Medical Supplies' => 'مستلزمات طبية',
                'Optics' => 'نظارات وبصريات',
            ]],
            ['orders', 'Retail & Shops', 'تجزئة ومتاجر', 'retail', 6, [
                'Fashion & Clothing' => 'أزياء وملابس', 'Shoes & Bags' => 'أحذية وحقائب', 'Electronics' => 'إلكترونيات',
                'Gifts & Flowers' => 'هدايا وورود', 'Perfumes & Cosmetics' => 'عطور ومستحضرات تجميل',
                'Home & Furniture' => 'منزل وأثاث', 'Toys & Kids' => 'ألعاب وأطفال',
            ]],

            ['booking', 'Medical', 'طبي', 'clinic', 9, [
                'Hospital' => 'مستشفى', 'Clinic' => 'عيادة', 'Doctor' => 'طبيب', 'Dentist' => 'طبيب أسنان',
                'Radiology Centre' => 'مركز أشعة', 'Laboratory' => 'مختبر',
            ]],
            ['booking', 'Beauty & Wellness', 'تجميل وعناية', 'salon', 10, [
                'Salon' => 'صالون', 'Barber Shop' => 'حلاق', 'Spa' => 'سبا', 'Nails' => 'أظافر',
                'Skin Care Centre' => 'مركز عناية بالبشرة',
            ]],
            ['booking', 'Fitness & Sports', 'لياقة ورياضة', 'booking', 8, [
                'Gym' => 'نادي رياضي', 'Fitness Studio' => 'استوديو لياقة', 'Yoga Studio' => 'استوديو يوغا',
                'Sports Academy' => 'أكاديمية رياضية',
            ]],
            ['booking', 'Hospitality & Venues', 'ضيافة وقاعات', 'booking', 8, [
                'Hotel' => 'فندق', 'Chalet' => 'شاليه', 'Event Hall' => 'قاعة مناسبات', 'Meeting Room' => 'غرفة اجتماعات',
            ]],
            ['booking', 'Rentals', 'تأجير', 'booking', 8, [
                'Car Rental' => 'تأجير سيارات', 'Equipment Rental' => 'تأجير معدات', 'Bike Rental' => 'تأجير دراجات',
            ]],

            ['service', 'Professional Services', 'خدمات مهنية', 'service', 8, [
                'Lawyer' => 'محامي', 'Designer' => 'مصمم', 'Developer' => 'مطور', 'Photographer' => 'مصور',
                'Accountant' => 'محاسب', 'Consultant' => 'مستشار', 'Translator' => 'مترجم',
            ]],
            ['service', 'Home Services', 'خدمات منزلية', 'service', 8, [
                'Cleaning' => 'تنظيف', 'Plumbing' => 'سباكة', 'Electrical' => 'كهرباء',
                'AC Maintenance' => 'صيانة تكييف', 'Painting' => 'دهان', 'Pest Control' => 'مكافحة حشرات',
                'Moving' => 'نقل أثاث',
            ]],
            ['service', 'Drivers & Delivery Services', 'سائقون وخدمات توصيل', 'service', 8, [
                'Independent Driver' => 'سائق مستقل', 'Delivery Driver' => 'سائق توصيل',
                'Driver Available Per Delivery' => 'سائق حسب الطلب', 'Daily Driver' => 'سائق يومي',
                'Weekly Driver' => 'سائق أسبوعي', 'Monthly Contract Driver' => 'سائق بعقد شهري',
                'Delivery Company' => 'شركة توصيل', 'Business Delivery Contract' => 'عقد توصيل للشركات',
            ]],
            ['service', 'Education & Training', 'تعليم وتدريب', 'service', 8, [
                'Private Tutor' => 'مدرس خصوصي', 'Training Centre' => 'مركز تدريب',
                'Language Instructor' => 'مدرس لغات', 'Music Instructor' => 'مدرس موسيقى',
            ]],
            ['service', 'Maintenance & Technical', 'صيانة وتقنية', 'service', 8, [
                'Car Mechanic' => 'ميكانيكي سيارات', 'Mobile & Device Repair' => 'صيانة أجهزة وهواتف',
                'Appliance Repair' => 'صيانة أجهزة منزلية', 'IT Support' => 'دعم تقني',
            ]],
        ];

        $order = 0;
        foreach ($catalogue as [$system, $name, $nameAr, $businessType, $template, $specs]) {
            $activityId = DB::table('activities')->insertGetId([
                'system'        => $system,
                'name'          => $name,
                'name_ar'       => $nameAr,
                'slug'          => \Illuminate\Support\Str::slug($name),
                'business_type' => $businessType,
                'template'      => $template,
                'reorder_id'    => ++$order,
                'is_available'  => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            $s = 0;
            foreach ($specs as $spec => $specAr) {
                DB::table('specializations')->insert([
                    'activity_id' => $activityId,
                    'name'        => $spec,
                    'name_ar'     => $specAr,
                    'slug'        => \Illuminate\Support\Str::slug($spec),
                    'reorder_id'  => ++$s,
                    'is_available' => 1,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('specializations');
        Schema::dropIfExists('activities');
    }
};
