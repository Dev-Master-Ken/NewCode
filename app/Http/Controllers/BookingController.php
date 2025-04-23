<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class BookingController extends Controller
{
    // Get all bookings (Accessible by everyone)
    public function index()
    {
        return Booking::all();
    }

    // Create a new booking (Renter only)
    public function store(Request $request)
    {
        // Ensure the user is a renter
        if (Gate::denies('renter')) {
            return response()->json(['message' => 'You are not authorized to create a booking.'], 403);
        }

        // Validate incoming request
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'pickup_date' => 'required|date',
            'return_date' => 'required|date|after:pickup_date',
        ]);

        // Add renter_id automatically from the authenticated user
        $bookingData = $request->all();
        $bookingData['renter_id'] = Auth::id(); // Add the authenticated user's ID as the renter_id

        // Calculate total_price
        $vehicle = \App\Models\Vehicle::findOrFail($bookingData['vehicle_id']); // Use the Vehicle model to find the vehicle
        $pickupDate = \Carbon\Carbon::parse($bookingData['pickup_date']);
        $returnDate = \Carbon\Carbon::parse($bookingData['return_date']);
        $duration = $pickupDate->diffInDays($returnDate); // Calculate the number of days
        $totalPrice = $vehicle->price_per_day * $duration; // Calculate total price

        $bookingData['total_price'] = $totalPrice; // Set the total price

        // Create the booking
        $booking = Booking::create($bookingData); // Create the booking with total_price

        return response()->json(['message' => 'Booking created successfully', 'booking' => $booking]);
    }

    // Update booking status (Owner only)
    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        // Ensure the user is an owner
        if (Gate::denies('owner')) {
            return response()->json(['message' => 'You are not authorized to update this booking.'], 403);
        }

        // Validate incoming request
        $request->validate([
            'status' => 'required|string|in:confirmed,cancelled', // Allow 'confirmed' and 'cancelled'
            // Add other status update validation as needed
        ]);

        // Update the booking status
        $booking->update($request->all());

        return response()->json(['message' => 'Booking status updated successfully']);
    }

    // Cancel booking (Renter only)
    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);

        // Ensure the user is a renter
        if (Gate::denies('renter')) {
            return response()->json(['message' => 'You are not authorized to cancel this booking.'], 403);
        }

        // Delete the booking
        $booking->delete();

        return response()->json(['message' => 'Booking cancelled successfully']);
    }
}
