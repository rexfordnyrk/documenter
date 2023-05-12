<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Signatory extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'email',
        'position',
        'signature_request_id',
        'ds_signature_id',
        'status',
    ];


    public function signatureRequest(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(SignatureRequest::class);
    }
}
