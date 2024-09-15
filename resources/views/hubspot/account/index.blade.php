<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="container">
            <div class="card shadow-sm">
                <div class="card-body">
                    {{-- HubSpot Connect Button --}}
                    <div class="mb-4 text-center">
                        <a href="{{ route('hubspot.authenticate') }}" 
                           class="btn btn-primary">
                           Connect HubSpot
                        </a>
                    </div>

                    {{-- List of Connected HubSpot Accounts --}}
                    <div id="hubspot-accounts" class="mt-4">
                        <h3 class="font-weight-bold mb-3">Connected HubSpot Accounts</h3>
                        <ul id="account-list" class="list-group">
                            <!-- Accounts will be loaded via Ajax -->
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    $(document).ready(function () {
        // Fetch HubSpot Accounts via Ajax
        $.get('{{ route('hubspot.accounts') }}', function (data) {
            const accountList = $('#account-list');
            accountList.empty(); // Clear previous data

            if (data.length === 0) {
                accountList.append('<li class="list-group-item">No accounts connected</li>');
            } else {
                const hubspotContactsRoute = @json(route('hubspot.contacts.index', ':id'));
                // Loop through each account and display it
                data.forEach(function (account) {
                    let accountUrl = hubspotContactsRoute.replace(':id', account.id);
                    accountList.append(`
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><strong>Account ID:</strong> ${account.hubspot_account_id}</span>
                             <a href="${accountUrl}" class="btn btn-link text-primary">
                                Contacts
                            </a>
                        </li>
                    `);
                });
            }
        });
    });
</script>
