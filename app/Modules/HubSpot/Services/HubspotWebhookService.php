<?php

namespace App\Modules\HubSpot\Services;

use App\Modules\HubSpot\Classes\HubSpotApiConnector;
use App\Modules\HubSpot\Repositories\HubspotAccountRepository;
use App\Modules\HubSpot\Repositories\HubspotContactRepository;

class HubspotWebhookService
{
    protected $hubSpotContactRepository;
    protected $hubSpotAccountRepository;

    public function __construct()
    {
        $this->hubSpotContactRepository = new HubspotContactRepository();
        $this->hubSpotAccountRepository = new HubspotAccountRepository();
    }

  
    public function processWebhook($events)
    {
        foreach ($events as $event) {
            $objectId = $event['objectId'];
            $subscriptionType = $event['subscriptionType'];
            $portalId = $event['portalId'];

            switch ($subscriptionType) {
                case 'contact.creation':
                    $this->handleContactCreation($objectId,$portalId);
                    break;

                case 'contact.propertyChange':
                    $this->handleContactPropertyChange($objectId, $event['propertyName'], $event['propertyValue']);
                    break;

                case 'contact.deletion':
                    $this->handleContactDeletion($objectId);
                    break;

                default:
                    break;
            }
        }
    }


    protected function handleContactCreation($objectId,$portalId)
    {
        $contact = $this->hubSpotContactRepository->getContactByHubSpotId($objectId);
        if (!$contact) {
            $hubspotAccount = $this->hubSpotAccountRepository->getAccountByHubSpotId($portalId);
            if ($hubspotAccount) {
                $apiConnector = new HubSpotApiConnector();
                $hubspot_contact = $apiConnector->getContact($portalId,$objectId);
                if (isset($hubspot_contact['id'])) {
                    $this->hubSpotContactRepository->createContact($hubspotAccount->id, $hubspot_contact);
                }
            }
        }
    }

    protected function handleContactPropertyChange($objectId, $propertyName, $propertyValue)
    {
        $contact = $this->hubSpotContactRepository->getContactByHubSpotId($objectId);
        if ($contact) {
            $contact->$propertyName = $propertyValue;
            $contact->update();
        }
    }

    protected function handleContactDeletion($objectId)
    {
        $contact = $this->hubSpotContactRepository->getContactByHubSpotId($objectId);
        if ($contact) {
            $contact->delete();
        }
    }
  
}
