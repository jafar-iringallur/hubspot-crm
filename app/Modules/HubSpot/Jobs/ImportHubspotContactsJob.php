<?php

namespace App\Modules\HubSpot\Jobs;

use App\Models\HubspotContact;
use App\Modules\HubSpot\Classes\HubSpotApiConnector;
use App\Modules\HubSpot\Repositories\HubspotAccountRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportHubspotContactsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $accountId;
    protected $after;
    /**
     * Create a new job instance.
     */
    public function __construct($accountId,$after=NULL)
    {
        $this->accountId = $accountId;
        $this->after = $after;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $apiConnector = new HubSpotApiConnector();
        $hubSpotAccountRepository = new HubspotAccountRepository();
        $hubspotAccount = $hubSpotAccountRepository->getAccountByHubSpotId($this->accountId);
        $contacts = $apiConnector->getContacts($this->accountId,$this->after);
        if (isset($contacts['results'])) {
            foreach ($contacts['results'] as $contact) {
                HubspotContact::updateOrCreate(
                    ['hubspot_contact_id' => $contact['id']],
                    [
                        'hubspot_account_id' => $hubspotAccount->id,
                        'firstname' => $contact['properties']['firstname'] ?? '',
                        'lastname' => $contact['properties']['lastname'] ?? '',
                        'email' => $contact['properties']['email'] ?? '',
                    ]
                );
            }
        }
        if(isset($contacts['paging']['next']['after'])){
            ImportHubspotContactsJob::dispatch($this->accountId,$contacts['paging']['next']['after']);
        }
    }
}
