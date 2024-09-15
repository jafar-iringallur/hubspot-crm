<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('HubSpot Account Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3>HubSpot Account ID: {{ $account->hubspot_account_id }}</h3>
                    <p>Token Expires At: {{ $account->token_expires_at }}</p>
                    <!-- You can add more details about the account here -->
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
