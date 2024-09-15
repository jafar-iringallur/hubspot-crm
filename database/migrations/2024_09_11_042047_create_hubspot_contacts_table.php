<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hubspot_contacts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hubspot_account_id');
            $table->string('hubspot_contact_id')->unique(); 
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
            
            $table->index('hubspot_contact_id');
            $table->index('hubspot_account_id');

            $table->foreign('hubspot_account_id')->references('id')->on('hubspot_accounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hubspot_contacts');
    }
};
