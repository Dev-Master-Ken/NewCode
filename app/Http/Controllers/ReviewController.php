<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ReviewController extends Controller
{
    // Get all reviews (Accessible by everyone)
    public function index()
    {
        return Review::all();
    }

    // Add a review (Renter only)
    public function store(Request $request)
    {
        // Ensure the user is a renter
        if (Gate::denies('renter')) {
            return response()->json(['message' => 'You are not authorized to add a review.'], 403);
        }

        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string',
        ]);

        // Add renter_id automatically from the authenticated user
        $reviewData = $request->all();
        $reviewData['renter_id'] = Auth::id(); // Add the authenticated user's ID as the renter_id

        // Create the review
        $review = Review::create($reviewData);

        return response()->json(['message' => 'Review added successfully', 'review' => $review]);
    }

    // Update a review (Renter only)
    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);

        // Ensure the user is a renter
        if (Gate::denies('renter')) {
            return response()->json(['message' => 'You are not authorized to update this review.'], 403);
        }

        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string',
        ]);

        // Update the review
        $review->update($request->all());

        return response()->json(['message' => 'Review updated successfully']);
    }

    // Delete a review (Renter only)
    public function destroy($id)
    {
        $review = Review::findOrFail($id);

        // Ensure the user is a renter
        if (Gate::denies('renter')) {
            return response()->json(['message' => 'You are not authorized to delete this review.'], 403);
        }

        // Delete the review
        $review->delete();

        return response()->json(['message' => 'Review deleted successfully']);
    }
}
