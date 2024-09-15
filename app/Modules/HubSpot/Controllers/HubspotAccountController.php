<?php

namespace App\Modules\HubSpot\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HubSpot\Services\HubspotAccountService;
use Illuminate\Http\Request;

class HubspotAccountController extends Controller
{
    protected $hubSpotAccountService;

    public function __construct(HubspotAccountService $hubSpotAccountService)
    {
        $this->hubSpotAccountService = $hubSpotAccountService;
    }

    public function index()
    {
        return view('hubspot.account.index');
    }

    public function authenticate(Request $request)
    {
        return redirect($this->hubSpotAccountService->getAuthUrl());
    }

    public function callback(Request $request)
    {
        $response = $this->hubSpotAccountService->callback($request->code);
        if ($response) {
            return redirect()->route('hubspot.index')->with('success', 'HubSpot account connected successfully!');
        } else {
            return redirect()->route('dashboard')->with('error', 'Error connecting to HubSpot.');
        }
    }

    public function getAccounts()
    {
        $hubspotAccounts = $this->hubSpotAccountService->getAccounts();
        return response()->json($hubspotAccounts);
    }
}
