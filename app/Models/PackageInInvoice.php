<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageInInvoice extends Model
{
    protected $table = 'packages_in_invoice'; // Explicit table name

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'invoice_id',
        'package_id',
    ];

    /**
     * Get the invoice that owns the package.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the package associated with the invoice.
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }
}