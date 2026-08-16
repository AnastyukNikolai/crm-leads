<?php

namespace Database\Seeders;

use App\Enums\CallResult;
use App\Enums\LeadStatus;
use App\Models\Call;
use App\Models\Lead;
use App\Models\Manager;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $manager = Manager::create(['name' => 'Alice Johnson']);
        Manager::create(['name' => 'Bob Smith']);

        $lead1 = Lead::create([
            'name' => 'John Doe',
            'phone' => '+380991234567',
            'status' => LeadStatus::New,
        ]);

        $lead2 = Lead::create([
            'name' => 'Jane Smith',
            'phone' => '+380992345678',
            'status' => LeadStatus::New,
        ]);

        $lead3 = Lead::create([
            'name' => 'Bob Wilson',
            'phone' => '+380993456789',
            'status' => LeadStatus::InProgress,
            'manager_id' => $manager->id,
        ]);

        Call::create([
            'lead_id' => $lead3->id,
            'manager_id' => $manager->id,
            'duration' => 180,
            'result' => CallResult::CallbackLater,
        ]);
    }
}
