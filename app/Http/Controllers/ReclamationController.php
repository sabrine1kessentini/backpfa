<?php

namespace App\Http\Controllers;

use App\Models\Reclamation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReclamationController extends Controller
{
    /**
     * Store a newly created reclamation in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
            ]);

            $reclamation = Reclamation::create([
                'user_id' => Auth::id(),
                'title' => $request->title,
                'description' => $request->description,
                'status' => 'pending'
            ]);

            return response()->json([
                'message' => 'Réclamation créée avec succès',
                'reclamation' => $reclamation
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la réclamation: ' . $e->getMessage());
            return response()->json([
                'message' => 'Une erreur est survenue lors de la création de la réclamation'
            ], 500);
        }
    }

    /**
     * Display the specified reclamation.
     */
    public function show(Reclamation $reclamation)
    {
        try {
            return response()->json($reclamation);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération de la réclamation: ' . $e->getMessage());
            return response()->json([
                'message' => 'Une erreur est survenue lors de la récupération de la réclamation'
            ], 500);
        }
    }

    /**
     * Get all reclamations for the authenticated user.
     */
    public function userReclamations()
    {
        try {
            $reclamations = Auth::user()->reclamations()->latest()->get();
            return response()->json($reclamations);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des réclamations: ' . $e->getMessage());
            return response()->json([
                'message' => 'Une erreur est survenue lors de la récupération des réclamations'
            ], 500);
        }
    }

    /**
     * Update the status of a reclamation.
     */
    public function updateStatus(Request $request, Reclamation $reclamation)
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,in_progress,resolved'
            ]);

            $reclamation->update([
                'status' => $request->status
            ]);

            return response()->json([
                'message' => 'Statut de la réclamation mis à jour',
                'reclamation' => $reclamation
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du statut: ' . $e->getMessage());
            return response()->json([
                'message' => 'Une erreur est survenue lors de la mise à jour du statut'
            ], 500);
        }
    }
} 