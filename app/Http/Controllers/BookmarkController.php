<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class BookmarkController extends Controller
{
    // Get all bookmarks (Accessible by everyone)
    public function index()
    {
        return Bookmark::where('renter_id', Auth::id())->get();
    }

    // Add a bookmark vehicle (Renter only)
    public function store(Request $request)
    {
        // Ensure the user is a renter
        if (Gate::denies('renter')) {
            return response()->json(['message' => 'You are not authorized to add a bookmark.'], 403);
        }

        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
        ]);

        // Create the bookmark with renter_id
        $bookmark = Bookmark::create([
            'renter_id' => Auth::id(), // Ensure the renter's ID is set correctly
            'vehicle_id' => $request->vehicle_id,
        ]);

        return response()->json(['message' => 'Bookmark added successfully', 'bookmark' => $bookmark]);
    }

    // Remove a bookmark vehicle (Renter only)
    public function destroy($id)
    {
        // Ensure the user is a renter
        if (Gate::denies('renter')) {
            return response()->json(['message' => 'You are not authorized to remove this bookmark.'], 403);
        }

        $bookmark = Bookmark::findOrFail($id);

        // Ensure the authenticated user owns the bookmark
        if ($bookmark->renter_id !== Auth::id()) {
            return response()->json(['message' => 'You are not authorized to delete this bookmark.'], 403);
        }

        // Delete the bookmark
        $bookmark->delete();

        return response()->json(['message' => 'Bookmark removed successfully']);
    }
}

