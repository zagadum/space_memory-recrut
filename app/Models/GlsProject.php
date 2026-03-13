<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GlsProject extends Model
{
    protected $table = 'gls_projects';

    protected $fillable = [
        'code',
        'name',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(GlsPaymentTransaction::class, 'project_id');
    }

    public function invoiceDocuments(): HasMany
    {
        return $this->hasMany(GlsInvoiceDocument::class, 'project_id');
    }
}
