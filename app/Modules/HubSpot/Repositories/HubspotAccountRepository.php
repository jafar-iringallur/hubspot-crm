<?php

namespace App\Modules\HubSpot\Repositories;

use App\Models\HubspotAccount;

class HubspotAccountRepository
{
    public function saveAccount($hub_id, array $data, $userId)
    {
        return HubspotAccount::updateOrCreate([
            'hubspot_account_id' => $hub_id
        ], [
            'access_token' => $data['access_token'],
            'refresh_token' => $data['refresh_token'],
            'token_expires_at' => now()->addSeconds($data['expires_in']),
            'user_id' => $userId,
        ]);
    }

    public function getAccounts($userId)
    {
        return HubspotAccount::where('user_id', $userId)->select(['id','hubspot_account_id'])->get();
    }

    public function getAccount($accountId, $userId)
    {
        return HubspotAccount::where('id', $accountId)->where('user_id', $userId)->first();
    }

    public function getAccountByHubSpotId($accountId)
    {
        return HubspotAccount::where('hubspot_account_id', $accountId)->first();
    }
}
