<?php 

namespace App\UseCases;

use App\Interfaces\TripInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Interfaces\UserInterface;
use App\Models\User;

class UserUseCase implements UserInterface 
{
    public function insert(array $payload) 
    {
        return DB::transaction(function () use ($payload) {
            $model = new User();
            $model->name = $payload['name'];
            $model->email = $payload['email'];
            $model->password = Hash::make($payload['password']);
            $model->save();

            $token = $model->createToken('auth_token')->plainTextToken;


            return [
                'access_token' => $token,
                'token_type' => 'Bearer',
            ];
        });
    }

    public function selectUser(array $payload)
    {
        $model = User::where('email', $payload['email'])->firstOrFail();
        
        $token = $model->createToken('auth_token')->plainTextToken;
    
        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
    }
}