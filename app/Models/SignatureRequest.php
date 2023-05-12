<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SignatureRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'document',
        'initiator_id',
        'status',
        'document_type_id',
        'reference_id',

    ];

    public function initiator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'initiator_id');
    }

    public function documentType(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function signatories(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Signatory::class);
    }
}
