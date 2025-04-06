<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplement;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SupplementController extends Controller
{
    // Get all supplements for the logged-in user
    public function index()
    {
        $supplements = Auth::user()->supplements;
        return response()->json($supplements);
    }

    // Store a new supplement
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'form' => 'required|string',
            'reason' => 'nullable|string',
            'frequency' => 'required|string',
            'time' => 'required|string',
            'duration' => 'nullable|integer',
            'refill_reminder' => 'nullable|boolean',
            'instructions' => 'nullable|string',
            'icon' => 'nullable|string',
        ]);

        $supplement = Auth::user()->supplements()->create($validated);

        return response()->json([
            'message' => 'Supplement added successfully',
            'supplement' => $supplement,
        ]);
    }

    public function show($id): JsonResponse
    {
        $supplement = Auth::user()->supplements()->where('id', $id)->first();
    
        if (!$supplement) {
            return response()->json(['message' => 'Supplement not found'], 404);
        }
    
        return response()->json($supplement);
    }

    public function update(Request $request, $id)
    {
        try {
            // Validate input
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'form' => 'required|string|max:255',
                'reason' => 'nullable|string',
                'frequency' => 'required|string|max:255',
                'time' => 'required|string|max:255',
                'duration' => 'nullable|string|max:255',
                'refill_reminder' => 'boolean',
                'instructions' => 'nullable|string',
                'icon' => 'nullable|string|max:255',
            ]);
    
            // Find the supplement for the authenticated user
            $supplement = Auth::user()->supplements()->findOrFail($id);
    
            // Update the supplement
            $supplement->update($validatedData);
    
            return response()->json([
                'message' => 'Supplement updated successfully',
                'data' => $supplement,
            ], 200);
    
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Delete a supplement
    public function destroy($id)
    {
        Auth::user()->supplements()->findOrFail($id)->delete();
        return response()->json(['message' => 'Supplement deleted successfully']);
    }
}

