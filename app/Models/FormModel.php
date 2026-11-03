<?php

namespace App\Models;

use Database\Factories\FormModelFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormModel extends Model
{
    /** @use HasFactory<FormModelFactory> */
    use HasFactory;

    protected static function newFactory(): FormModelFactory
    {
        return FormModelFactory::new();
    }

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
