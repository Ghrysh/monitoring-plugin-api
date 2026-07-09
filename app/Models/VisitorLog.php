<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitorLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'ip_address',
        'user_agent',
        'device',
        'browser',
        'os',
        'country',
        'city',
        'page_journey',
        'session_id',
        'date'
    ];

    protected $casts = [
        'page_journey' => 'array',
        'date' => 'date'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
