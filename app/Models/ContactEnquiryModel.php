<?php

namespace App\Models;

use Database\Factories\ContactEnquiryModelFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactEnquiryModel extends Model
{
    /** @use HasFactory<ContactEnquiryModelFactory> */
    use HasFactory;

    protected static function newFactory(): ContactEnquiryModelFactory
    {
        return ContactEnquiryModelFactory::new();
    }

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
