<?php
namespace Database\Seeders;
use App\Models\GlsPaymentPlan;
use App\Models\GlsProject;
use Illuminate\Database\Seeder;
class GlsPaymentPlansSeeder extends Seeder
{
    public function run(): void
    {
        $defaultPlans = [
            ['months' => 1,  'lessons' => 4,  'price' => 440.00,  'currency' => 'PLN', 'is_featured' => false, 'sort_order' => 10],
            ['months' => 3,  'lessons' => 12, 'price' => 1200.00, 'currency' => 'PLN', 'is_featured' => false, 'sort_order' => 20],
            ['months' => 6,  'lessons' => 24, 'price' => 2356.00, 'currency' => 'PLN', 'is_featured' => true,  'sort_order' => 30],
            ['months' => 12, 'lessons' => 48, 'price' => 4390.00, 'currency' => 'PLN', 'is_featured' => false, 'sort_order' => 40],
        ];
        $projects = GlsProject::query()->get();
        foreach ($projects as $project) {
            foreach ($defaultPlans as $plan) {
                GlsPaymentPlan::query()->updateOrCreate(
                    [
                        'project_id' => $project->id,
                        'months' => $plan['months'],
                    ],
                    [
                        'lessons' => $plan['lessons'],
                        'price' => $plan['price'],
                        'currency' => $plan['currency'],
                        'is_active' => true,
                        'is_featured' => $plan['is_featured'],
                        'sort_order' => $plan['sort_order'],
                    ]
                );
            }
        }
    }
}