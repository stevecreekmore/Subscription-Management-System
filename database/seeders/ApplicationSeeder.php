<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Company;
use App\Models\DiscountRule;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample company
        $company = Company::firstOrCreate(
            [
                'slug' => 'acme-corp'
            ],
            [
                'name' => 'ACME Corporation',
                'description' => 'A sample company for testing',
                'contact_email' => 'contact@acme.com',
                'phone' => '+1-555-0123',
                'address' => '123 Main St, Anytown, USA',
                'is_active' => true,
            ],
            [
                'slug' => 'Super-Company'
            ],
            [
                'name' => 'Super Company',
                'description' => 'Another sample company for testing',
                'contact_email' => 'admin@super.com',
                'phone' => '+1-555-0123',
                'address' => '1212 Main St, Austin, USA',
                'is_active' => true,
            ],
            [
                'slug' => 'Big-Company'
            ],
            [
                'name' => 'Big Company',
                'description' => 'A big company for testing',
                'contact_email' => 'admin@big.com',
                'phone' => '+1-555-0123',
                'address' => '1232 Second St, San Diego, USA',
                'is_active' => true,
            ]
        );

        // Create applications
        $applications = [
            [
                'name' => 'App1',
                'slug' => 'app1',
                'description' => 'First application in the subscription system',
                'api_key' => 'app1_1qNIOSYXJUtJlOIjf98sVvTRMwRcMCPw',
                'api_secret' => bcrypt('app1_secret_demo'),
                'is_active' => true,
            ],
            [
                'name' => 'App2',
                'slug' => 'app2',
                'description' => 'Second application in the subscription system',
                'api_key' => 'app2_' . Str::random(32),
                'api_secret' => bcrypt('app2_secret_' . Str::random(32)),
                'is_active' => true,
            ],
            [
                'name' => 'App3',
                'slug' => 'app3',
                'description' => 'Third application in the subscription system',
                'api_key' => 'app3_' . Str::random(32),
                'api_secret' => bcrypt('app3_secret_' . Str::random(32)),
                'is_active' => true,
            ],
        ];

        foreach ($applications as $appData) {
            $app = Application::firstOrCreate(
                ['slug' => $appData['slug']],
                $appData
            );

            // Create subscription plans for each application
            $plans = [
                [
                    'name' => 'Basic Plan',
                    'slug' => 'basic',
                    'description' => 'Basic features for ' . $app->name,
                    'price' => 9.99,
                    'billing_period' => 'monthly',
                    'features' => ['Feature 1', 'Feature 2', 'Basic Support'],
                    'is_active' => true,
                ],
                [
                    'name' => 'Pro Plan',
                    'slug' => 'pro',
                    'description' => 'Professional features for ' . $app->name,
                    'price' => 19.99,
                    'billing_period' => 'monthly',
                    'features' => ['All Basic Features', 'Advanced Analytics', 'Priority Support'],
                    'is_active' => true,
                ],
                [
                    'name' => 'Enterprise Plan',
                    'slug' => 'enterprise',
                    'description' => 'Enterprise features for ' . $app->name,
                    'price' => 99.99,
                    'billing_period' => 'monthly',
                    'features' => ['All Pro Features', 'Custom Integrations', 'Dedicated Support'],
                    'is_active' => true,
                ],
            ];

            foreach ($plans as $planData) {
                $planData['application_id'] = $app->id;
                SubscriptionPlan::firstOrCreate(
                    ['application_id' => $app->id, 'slug' => $planData['slug']],
                    $planData
                );
            }
        }

        // Create discount rules
        DiscountRule::firstOrCreate([
            'name' => 'Multi-Subscription Discount',
            'description' => '10% discount for users with 3+ active subscriptions',
            'type' => 'subscription_count',
            'min_subscriptions' => 3,
            'discount_type' => 'percentage',
            'discount_value' => 10.00,
            'is_active' => true,
        ]);

        DiscountRule::firstOrCreate([
            'name' => 'High Spender Discount',
            'description' => '15% discount for users who spent $100+',
            'type' => 'total_spent',
            'min_total_spent' => 100.00,
            'discount_type' => 'percentage',
            'discount_value' => 15.00,
            'max_discount_amount' => 50.00,
            'is_active' => true,
        ]);

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'System Administrator',
                'password' => bcrypt('password'),
                'company_id' => $company->id,
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        // Create regular user
        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'company_id' => $company->id,
                'email_verified_at' => now(),
            ]
        );
        $user->assignRole('user');

        $user2 = User::firstOrCreate(
            ['email' => 'user2@example.com'],
            [
                'name' => 'Test User2',
                'password' => bcrypt('password'),
                'company_id' => $company->id,
                'email_verified_at' => now(),
            ]
        );
        $user2->assignRole('user');
    }
}
