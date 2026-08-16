<?php

namespace App\Http\Controllers;

use App\Enums\LeadStatus;
use App\Http\Requests\StoreLeadRequest;
use App\Http\Resources\LeadResource;
use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class LeadController extends Controller
{
    public function store(StoreLeadRequest $request): JsonResponse
    {
        $lead = Lead::create([
            ...$request->validated(),
            'status' => LeadStatus::New,
            'manager_id' => null,
        ]);

        return (new LeadResource($lead))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
