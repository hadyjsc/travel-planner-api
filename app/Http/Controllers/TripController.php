<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Trip;
use App\Interfaces\TripInterface;

class TripController extends Controller
{
    protected $iTrip;

    public function __construct(TripInterface $tripInterface) {
        $this->iTrip = $tripInterface;
    }

    public function list(Request $req) {
        try {
            $page = $req->input('page', 1);
            $perPage = $req->input('perPage', 10);
            $search = $req->input('search');
    
            $result = $this->iTrip->getList($page, $perPage, $search);
    
            return $this->sendResponse($result);
        } catch (\Throwable $th) {
            return $this->sendError('Invalid Request', null, $th->getMessage(), 400);
        }
    }

    public function create(Request $req) {
        try {
            $validatedData = $req->validate([
                'title' => 'required|string|max:255',
                'origin' => 'required|string|max:50',
                'destination' => 'required|string|max:50',
                'schedule_start_date' => 'required|date|before_or_equal:schedule_end_date',
                'schedule_end_date' => 'required|date|after_or_equal:schedule_start_date',
                'type' => 'required',
                'description' => 'required',
            ]);

            $result = $this->iTrip->insert($validatedData);

            return $this->createdResponse($result, 'Successfully.', 'Success to create your trip plan.');
        } catch (ValidationException $e) {
            return $this->sendError(null, $e->getMessage(), $e->errors());
        } catch (\Throwable $th) {
            return $this->sendError(null, null, $th->getMessage());
        }
    }

    public function update(Request $req, $id) {
        try {
            $validatedData = $req->validate([
                'title' => 'required|string|max:255',
                'origin' => 'required|string|max:50',
                'destination' => 'required|string|max:50',
                'schedule_start_date' => 'required|date|before_or_equal:schedule_end_date',
                'schedule_end_date' => 'required|date|after_or_equal:schedule_start_date',
                'type' => 'required',
                'description' => 'required',
            ]);

            $result = $this->iTrip->update($id, $validatedData);

            return $this->sendResponse($result, 'Successfully.', 'Success to update your trip plan.');
        } catch (ValidationException $e) {
            return $this->sendError(null, $e->getMessage(), $e->errors());
        } catch (\Throwable $th) {
            return $this->sendError(null, null, $th->getMessage());
        }
    }

    public function delete($id) {
        try {
            $result = $this->iTrip->delete($id);
            return $this->sendResponse($result, 'Successfully.', 'Success to delete your trip plan.');
        } catch (\Throwable $th) {
            return $this->sendError(null, null, $th->getMessage());
        }
    }
}
