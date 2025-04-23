<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class VehicleController extends Controller
{
    // Get all vehicles (Accessible by everyone)
    public function index()
    {
        return Vehicle::all();
    }

    // Add a new vehicle (Owner only)
    public function store(Request $request)
    {
        // Ensure the user is an owner
        if (Gate::denies('owner')) {
            return response()->json(['message' => 'You are not authorized to add a vehicle.'], 403);
        }

        // Validate incoming request
        $request->validate([
            'name' => 'required|string|max:255',
            'plate_number' => 'required|string|max:255|unique:vehicles',
            'model' => 'required|string|max:255',
            'fuel_type' => 'required|string|max:255',
            'price_per_day' => 'required|numeric',
            'location' => 'required|string|max:255',
        ]);

        // Add the owner_id field automatically from the authenticated user
        $vehicleData = $request->all();
        $vehicleData['owner_id'] = Auth::id(); // Add the authenticated user's ID as the owner_id

        // Create the vehicle
        $vehicle = Vehicle::create($vehicleData);

        return response()->json(['message' => 'Vehicle added successfully', 'vehicle' => $vehicle]);
    }

    // Update vehicle info (Owner only)
    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        // Ensure the user is an owner
        if (Gate::denies('owner')) {
            return response()->json(['message' => 'You are not authorized to update this vehicle.'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'plate_number' => 'required|string|max:255|unique:vehicles,plate_number,' . $vehicle->id,
            // Add other vehicle fields as needed
        ]);

        $vehicle->update($request->all());

        return response()->json(['message' => 'Vehicle updated successfully']);
    }

    // Delete vehicle (Owner only)
    public function destroy($id)
    {
        $vehicle = Vehicle::findOrFail($id);

        // Ensure the user is an owner
        if (Gate::denies('owner')) {
            return response()->json(['message' => 'You are not authorized to delete this vehicle.'], 403);
        }

        $vehicle->delete();

        return response()->json(['message' => 'Vehicle deleted successfully']);
    }
}
