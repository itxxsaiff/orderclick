<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The "How It Works" section became admin-managed, but `works` only ever had a single title /
 * sub_title, so whatever the admin typed appeared on the Arabic landing page too — an English
 * block under an Arabic heading.
 *
 * These columns are additive and nullable: nothing is dropped, renamed or rewritten, so Arabic
 * content already entered elsewhere on a live site is untouched. When an Arabic value is blank
 * the landing page falls back to the English one, so the page can never go empty.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('works', function (Blueprint $table) {
            if (!Schema::hasColumn('works', 'title_ar')) {
                $table->string('title_ar')->nullable()->after('title');
            }
            if (!Schema::hasColumn('works', 'sub_title_ar')) {
                $table->string('sub_title_ar')->nullable()->after('sub_title');
            }
        });

        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'work_title_ar')) {
                $table->string('work_title_ar')->nullable()->after('work_title');
            }
            if (!Schema::hasColumn('settings', 'work_subtitle_ar')) {
                $table->string('work_subtitle_ar')->nullable()->after('work_subtitle');
            }
        });

        $this->seedArabic();
    }

    /**
     * Pre-fill Arabic for the standard steps we shipped, matched on their exact English text.
     * A row the client wrote themselves will not match and is left blank for them to fill in,
     * and an Arabic value that is already present is never overwritten.
     */
    private function seedArabic(): void
    {
        $steps = [
            'Choose System & Activity' => [
                'title' => 'اختر النظام والنشاط',
                'sub'   => ['Select the system and activity that match your business.' => 'اختر النظام والنشاط المناسبين لعملك.'],
            ],
            'Select Plan & Payment' => [
                'title' => 'اختر الباقة وادفع',
                'sub'   => ['Choose the suitable plan and complete payment.' => 'اختر الباقة المناسبة وأكمل عملية الدفع.'],
            ],
            'Complete Dashboard Setup' => [
                'title' => 'أكمل إعداد لوحة التحكم',
                'sub'   => ['Complete setup and submit the business for activation.' => 'أكمل الإعداد وأرسل النشاط التجاري للتفعيل.'],
            ],
        ];

        foreach ($steps as $englishTitle => $arabic) {
            DB::table('works')
                ->where('title', $englishTitle)
                ->whereNull('title_ar')
                ->update(['title_ar' => $arabic['title']]);

            foreach ($arabic['sub'] as $englishSub => $arabicSub) {
                DB::table('works')
                    ->where('title', $englishTitle)
                    ->where('sub_title', $englishSub)
                    ->whereNull('sub_title_ar')
                    ->update(['sub_title_ar' => $arabicSub]);
            }
        }

        DB::table('settings')
            ->where('work_title', 'How Order Click Works')
            ->whereNull('work_title_ar')
            ->update(['work_title_ar' => 'كيف يعمل Order Click']);

        DB::table('settings')
            ->where('work_subtitle', 'Choose your system, select a plan, and complete your business setup.')
            ->whereNull('work_subtitle_ar')
            ->update(['work_subtitle_ar' => 'اختر نظامك، وحدد باقتك، ثم أكمل إعداد نشاطك التجاري.']);
    }

    /**
     * Deliberately empty. Dropping these columns would silently destroy Arabic copy the client
     * has written, and nothing else in the app depends on them being absent.
     */
    public function down(): void
    {
        //
    }
};
