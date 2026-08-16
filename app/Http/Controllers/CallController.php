<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCallRequest;
use App\Http\Resources\CallResource;
use App\Models\Lead;
use App\Services\CallService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CallController extends Controller
{
    public function __construct(
        private readonly CallService $callService,
    ) {}

    public function store(StoreCallRequest $request, Lead $lead): JsonResponse
    {
        $call = $this->callService->createCall($lead, $request->validated());

        return (new CallResource($call))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
