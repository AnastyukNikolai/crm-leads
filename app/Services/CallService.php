<?php

namespace App\Services;

use App\Models\Call;
use App\Models\Lead;
use Illuminate\Support\Facades\DB;

class CallService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function createCall(Lead $lead, array $data): Call
    {
        return DB::transaction(function () use ($lead, $data) {
            return $lead->calls()->create($data);
        });
    }
}
