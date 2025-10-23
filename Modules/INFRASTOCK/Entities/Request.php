<?php

namespace Modules\INFRASTOCK\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class Request extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'productive_unit_warehouse_id',
        'description',
        'status',
        'approved_at',
        'rejected_at',
        'approved_by',
        'rejection_reason',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    /**
     * Get the user who made the request.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the productive unit warehouse.
     */
    public function productiveUnitWarehouse()
    {
        return $this->belongsTo(ProductiveUnitWarehouse::class);
    }

    /**
     * Get the user who approved the request.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the items for this request.
     */
    public function items()
    {
        return $this->hasMany(RequestItem::class);
    }

    /**
     * Get the total number of items in this request.
     */
    public function getTotalItemsAttribute()
    {
        return $this->items->count();
    }

    /**
     * Get the total requested amount across all items.
     */
    public function getTotalRequestedAmountAttribute()
    {
        return $this->items->sum('requested_amount');
    }

    /**
     * Scope a query to only include requests by a specific user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include requests with a specific status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include approved requests.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include rejected requests.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
