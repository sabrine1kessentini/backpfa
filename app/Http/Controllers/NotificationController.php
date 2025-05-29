<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Tymon\JWTAuth\Facades\JWTAuth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            
            if (!$user) {
                return response()->json(['error' => 'Utilisateur non authentifié'], 401);
            }

            $notifications = Notification::whereHas('targets', function($query) use ($user) {
                    $query->where('target_type', 'all')
                        ->orWhere([
                            ['target_type', 'filiere'],
                            ['target_value', $user->filiere]
                        ])
                        ->orWhere([
                            ['target_type', 'level'],
                            ['target_value', $user->niveau]
                        ])
                        ->orWhere([
                            ['target_type', 'groupe'],
                            ['target_value', $user->groupe]
                        ]);
                })
                ->with('targets')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($notifications);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la récupération des notifications',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function unreadCount(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            
            if (!$user) {
                return response()->json(['error' => 'Utilisateur non authentifié'], 401);
            }

            $count = Notification::whereHas('targets', function($query) use ($user) {
                    $query->where('target_type', 'all')
                        ->orWhere([
                            ['target_type', 'filiere'],
                            ['target_value', $user->filiere]
                        ])
                        ->orWhere([
                            ['target_type', 'level'],
                            ['target_value', $user->niveau]
                        ])
                        ->orWhere([
                            ['target_type', 'groupe'],
                            ['target_value', $user->groupe]
                        ]);
                })
                ->whereNull('read_at')
                ->count();

            return response()->json(['count' => $count]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors du comptage des notifications',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function markAsRead($id)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            
            if (!$user) {
                return response()->json(['error' => 'Utilisateur non authentifié'], 401);
            }

            $notification = Notification::findOrFail($id);
            $notification->update(['read_at' => now()]);
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors du marquage de la notification',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function markAllAsRead(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            
            if (!$user) {
                return response()->json(['error' => 'Utilisateur non authentifié'], 401);
            }

            Notification::whereHas('targets', function($query) use ($user) {
                    $query->where('target_type', 'all')
                        ->orWhere([
                            ['target_type', 'filiere'],
                            ['target_value', $user->filiere]
                        ])
                        ->orWhere([
                            ['target_type', 'level'],
                            ['target_value', $user->niveau]
                        ])
                        ->orWhere([
                            ['target_type', 'groupe'],
                            ['target_value', $user->groupe]
                        ]);
                })
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors du marquage des notifications',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            
            if (!$user) {
                return response()->json(['error' => 'Utilisateur non authentifié'], 401);
            }

            $notification = Notification::findOrFail($id);
            $notification->delete();
            
            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la suppression de la notification',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}