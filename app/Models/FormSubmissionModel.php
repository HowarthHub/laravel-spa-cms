<?php

namespace App\Models;

use Database\Factories\FormSubmissionModelFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormSubmissionModel extends Model
{
    /** @use HasFactory<FormSubmissionModelFactory> */
    use HasFactory;

    protected static function newFactory(): FormSubmissionModelFactory
    {
        return FormSubmissionModelFactory::new();
    }

    protected $table = 'form_submissions';

    protected $fillable = ['form_id', 'data', 'ip_address', 'user_agent'];

    protected function casts(): array
    {
        return ['data' => 'array'];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(FormModel::class, 'form_id');
    }
}
