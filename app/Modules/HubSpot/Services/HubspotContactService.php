<?php

namespace App\Modules\HubSpot\Services;

use App\Modules\HubSpot\Classes\HubSpotApiConnector;
use App\Modules\HubSpot\Jobs\ImportHubspotContactsJob;
use App\Modules\HubSpot\Repositories\HubspotAccountRepository;
use App\Modules\HubSpot\Repositories\HubspotContactRepository;

class HubspotContactService
{
    protected $hubSpotContactRepository;
    protected $hubSpotAccountRepository;
    protected $hubSpotApiConnector;

    public function __construct()
    {
        $this->hubSpotContactRepository = new HubspotContactRepository();
        $this->hubSpotAccountRepository = new HubspotAccountRepository();
        $this->hubSpotApiConnector = new HubSpotApiConnector();
    }

    private function getUserId()
    {
        return auth()->user()->id ?? NULL;
    }

    public function getAccount($accountId)
    {
        $hubspotAccount = $this->hubSpotAccountRepository->getAccount($accountId, $this->getUserId());

        if (!$hubspotAccount) {
            return false;
        }
        return $hubspotAccount;
    }

    public function getContacts($accountId){
        $hubspotAccount = $this->hubSpotAccountRepository->getAccount($accountId, $this->getUserId());

        if (!$hubspotAccount) {
            return false;
        }
        return $this->hubSpotContactRepository->getContacts($accountId);
    }

    public function importContacts($accountId)
    {
        $hubspotAccount = $this->hubSpotAccountRepository->getAccount($accountId, $this->getUserId());

        if (!$hubspotAccount) {
            return false;
        }
        ImportHubspotContactsJob::dispatch($hubspotAccount->hubspot_account_id);
        return true;
    }


    public function createContact($data)
    {
        $hubspotAccount = $this->hubSpotAccountRepository->getAccount($data->accountId, $this->getUserId());

        if (!$hubspotAccount) {
            return false;
        }

        $hubspot_contact = $this->hubSpotApiConnector->createContact($hubspotAccount->hubspot_account_id, [
            'properties' => [
                'firstname' => $data->firstname,
                'lastname' => $data->lastname,
                'email' => $data->email,
            ]
        ]);
        if (isset($hubspot_contact['id'])) {
            $this->hubSpotContactRepository->createContact($data->accountId, $hubspot_contact);
            return true;
        }
        return false;
    }

    public function updateContact($id, $data)
    {
        $contact = $this->hubSpotContactRepository->getContact($id);
        if (!$contact) {
            return false;
        }
        $hubspotAccount = $this->hubSpotAccountRepository->getAccount($contact->hubspot_account_id, $this->getUserId());
        if (!$hubspotAccount) {
            return false;
        }
        $hubspot_contact = $this->hubSpotApiConnector->updateContact($hubspotAccount->hubspot_account_id, $contact->hubspot_contact_id, [
            'properties' => [
                'firstname' => $data->firstname,
                'lastname' => $data->lastname,
                'email' => $data->email,
            ]
        ]);
        if (isset($hubspot_contact['id'])) {
            $this->hubSpotContactRepository->updateContact($data->accountId, $hubspot_contact);
            return true;
        }
        return false;
    }
    public function deleteContact($id)
    {
        $contact = $this->hubSpotContactRepository->getContact($id);
        if (!$contact) {
            return false;
        }
        $hubspotAccount = $this->hubSpotAccountRepository->getAccount($contact->hubspot_account_id, $this->getUserId());
        if (!$hubspotAccount) {
            return false;
        }
        $this->hubSpotApiConnector->deleteContact($hubspotAccount->hubspot_account_id, $contact->hubspot_contact_id);

        $this->hubSpotContactRepository->deleteContact($contact->id);
        return true;
    }
}
