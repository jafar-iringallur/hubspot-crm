<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HubspotAccount extends Model
{
    use HasFactory;
    protected $fillable = [
        'hubspot_account_id', 
        'access_token', 
        'refresh_token', 
        'token_expires_at', 
        'user_id'
    ];
}
