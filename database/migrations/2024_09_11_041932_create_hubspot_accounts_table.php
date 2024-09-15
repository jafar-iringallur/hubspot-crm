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
        Schema::create('hubspot_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); 
            $table->string('hubspot_account_id')->unique();
            $table->string('access_token');
            $table->string('refresh_token');
            $table->timestamp('token_expires_at');
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('hubspot_account_id');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hubspot_accounts');
    }
};
