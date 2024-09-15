<?php

namespace App\Modules\HubSpot\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HubSpot\Services\HubspotWebhookService;
use Illuminate\Http\Request;

class HubspotWebhookController extends Controller
{
    protected $hubspotWebhookService;

    public function __construct(HubspotWebhookService $hubspotWebhookService)
    {
        $this->hubspotWebhookService = $hubspotWebhookService;
    }

    public function handle(Request $request)
    {
        $events = $request->all();

        $this->hubspotWebhookService->processWebhook($events);

        return response()->json(['status' => 'success'], 200);
    }

   
   
}
