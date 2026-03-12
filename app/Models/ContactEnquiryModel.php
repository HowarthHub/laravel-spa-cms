<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactEnquiryModel extends Model
{
    protected $table = 'contact_enquiries';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'status',
        'ip_address',
        'user_agent',
        'read_at',
        'replied_at',
        'reply_note',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
            'replied_at' => 'datetime',
        ];
    }
}
