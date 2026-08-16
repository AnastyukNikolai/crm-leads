<?php

namespace App\Services;

use App\Enums\CallResult;
use App\Enums\LeadStatus;
use App\Models\Call;
use App\Models\Lead;

class LeadStatusService
{
    private const CONSECUTIVE_NO_ANSWER_COUNT = 3;

    public function assignManagerIfNeeded(Lead $lead, int $managerId): void
    {
        if ($lead->manager_id !== null) {
            return;
        }

        $lead->update(['manager_id' => $managerId]);
    }

    public function updateStatusBasedOnCall(Lead $lead, Call $call): void
    {
        if ($lead->status->isTerminal()) {
            return;
        }

        $newStatus = $this->determineNewStatus($lead, $call);

        if ($newStatus !== null && $newStatus !== $lead->status) {
            $lead->update(['status' => $newStatus]);
        }
    }

    private function determineNewStatus(Lead $lead, Call $call): ?LeadStatus
    {
        if ($call->result === CallResult::Success) {
            return LeadStatus::Won;
        }

        if ($this->lastCallsAreConsecutiveNoAnswer($lead)) {
            return LeadStatus::Lost;
        }

        if ($this->isFirstCallAndNew($lead)) {
            return LeadStatus::InProgress;
        }

        return null;
    }

    private function isFirstCallAndNew(Lead $lead): bool
    {
        return $lead->status === LeadStatus::New && $lead->calls()->count() === 1;
    }

    private function lastCallsAreConsecutiveNoAnswer(Lead $lead): bool
    {
        $lastCalls = $lead->calls()
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->take(self::CONSECUTIVE_NO_ANSWER_COUNT)
            ->get();

        if ($lastCalls->count() < self::CONSECUTIVE_NO_ANSWER_COUNT) {
            return false;
        }

        return $lastCalls->every(
            fn (Call $call) => $call->result === CallResult::NoAnswer
        );
    }
}
