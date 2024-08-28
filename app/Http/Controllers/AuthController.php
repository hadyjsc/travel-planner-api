<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Interfaces\UserInterface;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected $iUser;

    public function __construct(UserInterface $userInterface) {
        $this->iUser = $userInterface;
    }

    public function register(Request $req) {
        try {
            $validator =  $req->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);
        
            $result = $this->iUser->insert($validator);

            return $this->createdResponse($result, 'Successfully.', 'Success to register user.');
        } catch (ValidationException $e) {
            return $this->sendError(null, $e->getMessage(), $e->errors(), 400);
        } catch (\Throwable $th) {
            return $this->sendError(null, null, $th->getMessage());
        }
    }

    public function login(Request $req) {
        try {
            $validator =  $req->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            if (!Auth::attempt($req->only('email', 'password'))) {
                return $this->sendError('Invalid.', 'Invalid login details.', null, 401);
            }
        
            $result = $this->iUser->selectUser($validator);

            return $this->sendResponse($result, 'Successfully.', 'Successfully logged in.');
        } catch (ValidationException $e) {
            return $this->sendError(null, $e->getMessage(), $e->errors(), 400);
        } catch (\Throwable $th) {
            return $this->sendError(null, null, $th->getMessage());
        }
    }

    public function logout(Request $req) {
        try {
            $req->user()->currentAccessToken()->delete();
            return $this->sendResponse(null, 'Successfully.', 'Successfully logged out.');
        } catch (\Throwable $th) {
            return $this->sendError(null, null, $th->getMessage());
        }
    }
}
