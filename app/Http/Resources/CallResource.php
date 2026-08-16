<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CallResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'lead_id' => $this->lead_id,
            'manager_id' => $this->manager_id,
            'duration' => $this->duration,
            'result' => $this->result,
            'created_at' => $this->created_at,
        ];
    }
}
