<?php

namespace Modules\INFRASTOCK\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'equipment_id',
        'requested_amount',
        'approved_amount',
        'delivered_amount',
        'status',
        'notes',
    ];

    /**
     * Get the request that owns this item.
     */
    public function request()
    {
        return $this->belongsTo(Request::class);
    }

    /**
     * Get the equipment for this item.
     */
    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    /**
     * Scope a query to only include items with a specific status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include pending items.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include approved items.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include rejected items.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope a query to only include delivered items.
     */
    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    /**
     * Get the surpluses for this request item.
     */
    public function surpluses()
    {
        return $this->hasMany(Surplus::class, 'request_item_id');
    }
}
