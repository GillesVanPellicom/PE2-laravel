<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class Invoice extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'invoices';
    use HasFactory;
    protected $fillable = [
        'company_id',
        'discount',
        'expiry_date',
        'is_paid',
        'paid_at',
        'reference',
    ];

    /**
     * Get the company that owns the invoice.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
