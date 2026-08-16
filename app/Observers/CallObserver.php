<?php

namespace App\Observers;

use App\Models\Call;
use App\Services\LeadStatusService;

class CallObserver
{
    public function __construct(
        private readonly LeadStatusService $leadStatusService,
    ) {}

    public function created(Call $call): void
    {
        $lead = $call->lead;

        $this->leadStatusService->assignManagerIfNeeded($lead, $call->manager_id);
        $this->leadStatusService->updateStatusBasedOnCall($lead, $call);
    }
}
