<?php

namespace App\Modules\HubSpot\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HubSpot\Services\HubspotContactService;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class HubspotContactController extends Controller
{
    protected $hubSpotContactService;

    public function __construct(HubspotContactService $hubSpotContactService)
    {
        $this->hubSpotContactService = $hubSpotContactService;
    }

    public function index($id)
    {

        $account = $this->hubSpotContactService->getAccount($id);
        if (!$account) {
            return abort(404);
        }

        return view('hubspot.contacts.index', ['accountId' => $id]);
    }

    public function getContacts(Request $request,$id){
        $contacts = $this->hubSpotContactService->getContacts($id);
        if (!$contacts) {
            return abort(404);
        }
        return DataTables::of($contacts)->make(true);
    }

    public function import(Request $request)
    {
        $validatedData = $request->validate([
            'accountId' => 'required|integer',
        ]);
        $accountId = $validatedData['accountId'];
        $import_response = $this->hubSpotContactService->importContacts($accountId);
        if (!$import_response) {
            return response()->json(['error' => 'Failed to fetch contacts from HubSpot.'], 500);
        }
        return response()->json(['message' => 'Contacts imported successfully.']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|email',
            'accountId' => 'required'
        ]);

        $create_response = $this->hubSpotContactService->createContact($request);
        if (!$create_response) {
            return response()->json(['error' => 'Failed to create contact'], 500);
        }
        return response()->json(['message' => 'Contact added successfully.']);
    }
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|email',
        ]);

        $update_response = $this->hubSpotContactService->updateContact($id, $request);
        if (!$update_response) {
            return response()->json(['error' => 'Failed to update contact'], 500);
        }
        return response()->json(['message' => 'Contact updated successfully.']);
    }
    public function destroy($id)
    {
        $delete_response = $this->hubSpotContactService->deleteContact($id);
        if (!$delete_response) {
            return response()->json(['error' => 'Failed to delete contact'], 500);
        }
        return response()->json(['message' => 'Contact deleted successfully.']);
    }
}
