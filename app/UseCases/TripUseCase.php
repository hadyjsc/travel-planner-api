<?php 

namespace App\UseCases;

use App\Interfaces\TripInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\Models\Trip;
use App\Models\TripLog;

class TripUseCase implements TripInterface {
    protected $CACHE_KEY = 'trips';

    protected function clearCacheForUser(int $userId)
    {
        $pattern = "{$this->CACHE_KEY}:user:{$userId}:*";
        $keys = Redis::keys($pattern);
        foreach ($keys as $v) {
            $lower = strtolower($v);
            $deletedCount = Redis::del([$lower]);
        }
    }

    public function getList(int $page = 1, int $perPage = 15, string $search = null) {
        $KEY = "{$this->CACHE_KEY}:user:" . auth()->id() . ":page:{$page}:limit:{$perPage}";
        
        $trips = Redis::get($KEY);
        if (!$trips) {
            $query = Trip::query();
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%')
                      ->orWhere('description', 'like', '%' . $search . '%');
                });
            }
    
            $query->where('is_deleted', false);
            $query->where('created_by', auth()->id());
    
            $trips = $query->paginate($perPage, ['*'], 'page', $page);
            
            $redis = Redis::setex($KEY, 3600, serialize($trips));
        } else {
            $trips = unserialize($trips);
        }
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
            $model->created_by = auth()->id();
            $model->save();
    
            $log = new TripLog();
            $log->trip_id = $model->getKey();
            $log->original_data = json_encode($model->toArray());
            $log->action = 1; 
            $log->action_by = auth()->id();
            $log->action_at = now();
            $log->save();

            $this->clearCacheForUser(auth()->id());
    
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
            $model->updated_by = auth()->id();
            $model->save();

            $log = new TripLog();
            $log->trip_id = $model->getKey();
            $log->original_data = json_encode($originalData);
            $log->action = 2; 
            $log->action_by = auth()->id();
            $log->action_at = now();
            $log->save();

            $this->clearCacheForUser(auth()->id());

            return $model;
        });
    }

    public function delete(int $id) {
        return DB::transaction(function () use ($id) {
            $model = Trip::where('id', $id)->where('is_deleted', false)->where('created_by', auth()->id())->firstOrFail();

            $model->updated_by = auth()->id();
            $model->is_deleted = true;
            $model->save();

            $log = new TripLog();
            $log->trip_id = $model->getKey();
            $log->original_data = json_encode($model->toArray());
            $log->action = 0; 
            $log->action_by = auth()->id();
            $log->action_at = now();
            $log->save();

            $this->clearCacheForUser(auth()->id());

            return $model;
        });
    }
}