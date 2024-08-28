<?php 

namespace App\UseCases;

use App\Interfaces\TripInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\Trip;
use App\Models\TripLog;

class TripUseCase implements TripInterface {
    protected $CACHE_KEY = 'tripsGetList';
    public function getList(int $page = 1, int $perPage = 15, string $search = null) {
        $trips = Cache::remember($this->CACHE_KEY, 3600, function (int $page = 1, int $perPage = 15, string $search = null) {
            $query = Trip::query();
    
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%')->orWhere('description', 'like', '%' . $search . '%');
                });
            }
    
            $query->where('is_deleted', false);
    
            return $query->paginate($perPage, ['*'], 'page', $page);
        });
        return $trips;
    }

    public function insert(array $payload) {
        return DB::transaction(function () use ($payload) {
            $type = $payload['type'] === 'multi-day' ? 2 : 1;
            $model = new Trip();
            $model->title = $payload['title'];
            $model->origin = $payload['origin'];
            $model->destination = $payload['destination'];
            $model->schedule_start_date = $payload['schedule_start_date'];
            $model->schedule_end_date = $payload['schedule_end_date'];
            $model->type = $type;
            $model->description = $payload['description'];
            $model->is_deleted = false;
            $model->created_by = 1; // auth()->id()
            $model->save();
    
            $log = new TripLog();
            $log->trip_id = $model->getKey();
            $log->original_data = json_encode($model->toArray());
            $log->action = 1; // ADD = 1
            $log->action_by = 1; // auth()->id();
            $log->action_at = now();
            $log->save();

            Cache::forget($this->CACHE_KEY);
    
            return $model;
        });
    }

    public function update(int $id, array $payload) {
        return DB::transaction(function () use ($id, $payload) {
            $model = Trip::where('id', $id)->where('is_deleted', false)->firstOrFail();
            $originalData = $model->getAttributes();
            
            $type = $payload['type'] === 'multi-day' ? 2 : 1;
            $payload['type'] = $type;
            $model->fill($payload);
            $model->updated_by = 1; // auth()->id();
            $model->save();

            // Log the update action
            $log = new TripLog();
            $log->trip_id = $model->getKey();
            $log->original_data = json_encode($originalData);
            $log->action = 2; // UPDATE
            $log->action_by = 1; // auth()->id();
            $log->action_at = now();
            $log->save();

            Cache::forget($this->CACHE_KEY);

            return $model;
        });
    }

    public function delete(int $id) {
        return DB::transaction(function () use ($id) {
            // Find the model by ID
            $model = Trip::where('id', $id)->where('is_deleted', false)->firstOrFail();

            // Soft delete the model
            $model->updated_by = 1; // auth()->id();
            $model->is_deleted = true;
            $model->save();

            // Log the delete action
            $log = new TripLog();
            $log->trip_id = $model->getKey();
            $log->original_data = json_encode($model->toArray());
            $log->action = 0; // DELETED
            $log->action_by = 1; // auth()->id();
            $log->action_at = now();
            $log->save();

            Cache::forget($this->CACHE_KEY);

            return $model;
        });
    }
}