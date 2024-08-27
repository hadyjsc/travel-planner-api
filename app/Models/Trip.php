<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\TripLogModel;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'origin', 'destination', 'schedule_start_date', 'schedule_end_date', 'type', 'description', 'created_by', 'created_at'];

    protected $hidden = ['id', 'updated_at', 'updated_by', 'is_deleted'];

    /**
     * Get all of the users for the Trip
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'user_id', 'id');
    }

    /**
     * Get all of the tripLogs for the Trip
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tripLogs(): HasMany
    {
        return $this->hasMany(TripLogModel::class, 'trip_id', 'id');
    }
}
