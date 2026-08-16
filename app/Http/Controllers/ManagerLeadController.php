<?php

namespace App\Http\Controllers;

use App\Http\Resources\ManagerLeadResource;
use App\Models\Manager;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ManagerLeadController extends Controller
{
    public function index(Manager $manager): AnonymousResourceCollection
    {
        $leads = $manager->leads()
            ->withCount('calls')
            ->withSum('calls', 'duration')
            ->get();

        return ManagerLeadResource::collection($leads);
    }
}
