<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Users;

class TripLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['trip_id', 'original_data', 'action', 'action_by', 'action_at'];

    protected $hidden = ['id'];

    /**
    * Get all of the users for the TripLogModel
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function users(): HasMany
    {
        return $this->hasMany(Users::class, 'user_id', 'id');
    }

    /**
     * Get the trips that owns the TripLogModel
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function trips(): BelongsTo
    {
        return $this->belongsTo(Trip::class)->withDefault();
    }
}