<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\DealStage;
use App\Models\Lead;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Fieldstone Admin',
            'email' => 'admin@fieldstonecrm.com',
            'password' => Hash::make('fieldstone-demo'),
        ]);

        $stages = collect([
            'Lead In',
            'Contact Made',
            'Demo Scheduled',
            'Proposal Made',
            'Negotiations Started',
        ])->map(fn (string $name, int $index) => DealStage::create([
            'name' => $name,
            'sort_order' => $index,
        ]));

        $organizations = Organization::factory(10)->create(['owner_id' => $admin->id]);

        $contacts = $organizations->flatMap(
            fn (Organization $organization) => Contact::factory(random_int(1, 3))->create([
                'organization_id' => $organization->id,
                'owner_id' => $admin->id,
            ])
        );

        $deals = collect();

        foreach ($contacts as $contact) {
            if (fake()->boolean(70)) {
                $status = fake()->randomElement(['open', 'open', 'open', 'won', 'lost']);

                $deals->push(Deal::factory()->create([
                    'contact_id' => $contact->id,
                    'organization_id' => $contact->organization_id,
                    'deal_stage_id' => $stages->random()->id,
                    'status' => $status,
                    'owner_id' => $admin->id,
                ]));
            }
        }

        foreach ($contacts as $contact) {
            $count = random_int(0, 3);

            for ($i = 0; $i < $count; $i++) {
                $contactDeals = $deals->where('contact_id', $contact->id);
                $relatedDeal = $contactDeals->isNotEmpty() ? $contactDeals->random() : null;

                Activity::factory()->create([
                    'contact_id' => $contact->id,
                    'deal_id' => $relatedDeal?->id,
                    'owner_id' => $admin->id,
                ]);
            }
        }

        // A handful of guaranteed activities across every period bucket, so the
        // Activities filters (overdue / today / tomorrow / this week / next week) all have data.
        $periodFixtures = [
            ['due_date' => now()->subDays(3), 'done' => false],
            ['due_date' => now()->subDay(), 'done' => false],
            ['due_date' => now(), 'done' => false],
            ['due_date' => now()->addDay(), 'done' => false],
            ['due_date' => now()->addDays(4), 'done' => false],
            ['due_date' => now()->addDays(10), 'done' => false],
            ['due_date' => now()->subDays(2), 'done' => true],
        ];

        foreach ($periodFixtures as $fixture) {
            Activity::factory()->create(array_merge($fixture, [
                'contact_id' => $contacts->random()->id,
                'owner_id' => $admin->id,
            ]));
        }

        foreach (range(1, 8) as $i) {
            $contact = $contacts->random();

            Lead::factory()->create([
                'contact_id' => $contact->id,
                'organization_id' => $contact->organization_id,
                'owner_id' => $admin->id,
                'status' => $i <= 2 ? 'converted' : 'inbox',
                'converted_deal_id' => $i <= 2 ? ($deals->first()?->id) : null,
            ]);
        }
    }
}
