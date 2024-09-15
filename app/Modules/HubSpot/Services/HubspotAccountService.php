<?php

namespace App\Modules\HubSpot\Services;

use App\Modules\HubSpot\Classes\HubSpotApiConnector;
use App\Modules\HubSpot\Repositories\HubspotAccountRepository;

class HubspotAccountService
{
    protected $hubSpotApiConnector;
    protected $hubSpotAccountRepository;

    public function __construct()
    {
        $this->hubSpotApiConnector = new HubSpotApiConnector();
        $this->hubSpotAccountRepository = new HubspotAccountRepository();
    }

    private function getUserId()
    {
        return auth()->user()->id ?? NULL;
    }

    public function getAuthUrl()
    {
        return $this->hubSpotApiConnector->getAuthUrl();
    }

    public function callback($code)
    {
        $tokenData = $this->hubSpotApiConnector->getAccessToken($code);
        if (!isset($tokenData['access_token'])) {
            return false;
        }
        $accessToken = $tokenData['access_token'];
        $accountDetails = $this->hubSpotApiConnector->getAccountDetails($accessToken);
        if (!isset($accountDetails['hub_id'])) {
            return false;
        }
        $hub_id = $accountDetails['hub_id'];
        $this->hubSpotAccountRepository->saveAccount($hub_id, $tokenData, $this->getUserId());
        return true;
    }

    public function getAccounts()
    {
        return $this->hubSpotAccountRepository->getAccounts(auth()->user()->id);
    }
}
