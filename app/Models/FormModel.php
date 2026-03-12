<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormModel extends Model
{
    protected $table = 'forms';

    protected $fillable = ['name', 'handle', 'fields', 'success_message', 'notification_email', 'is_active'];

    protected function casts(): array
    {
        return [
            'fields' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(FormSubmissionModel::class, 'form_id');
    }
}
