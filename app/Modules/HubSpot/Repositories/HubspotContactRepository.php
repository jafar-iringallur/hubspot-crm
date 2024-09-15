<?php

namespace App\Modules\HubSpot\Repositories;

use App\Models\HubspotContact;

class HubspotContactRepository
{

    public function getContacts($hubspot_account_id)
    {
        return HubspotContact::where('hubspot_account_id', $hubspot_account_id);
    }

    public function getContact($id)
    {
        return HubspotContact::find($id);
    }
    public function getContactByHubSpotId($hubspot_contact_id)
    {
        return HubspotContact::where('hubspot_contact_id',$hubspot_contact_id)->first();
    }

    public function createContact($accountId, $contact)
    {
        return HubspotContact::create([
            'hubspot_account_id' => $accountId,
            'hubspot_contact_id' => $contact['id'],
            'firstname' => $contact['properties']['firstname'] ?? '',
            'lastname' => $contact['properties']['lastname'] ?? '',
            'email' => $contact['properties']['email'] ?? '',
        ]);
    }
    public function updateContact($id, $data)
    {
        $contact = HubspotContact::find($id);
        return $contact->update([
            'firstname' => $data['properties']['firstname'] ?? '',
            'lastname' => $data['properties']['lastname'] ?? '',
            'email' => $data['properties']['email'] ?? '',
        ]);
    }

    public function deleteContact($id)
    {
        return HubspotContact::find($id)->delete();;
    }
}
