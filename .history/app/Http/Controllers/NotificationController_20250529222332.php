<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        // Méthode CORRECTE pour votre structure personnalisée
        $notifications = Notification::whereHas('targets', function($query) use ($request) {
                $query->where('target_type', 'all')
                    ->orWhere([
                        ['target_type', 'filiere'],
                        ['target_value', $request->user()->filiere]
                    ])
                    ->orWhere([
                        ['target_type', 'level'],
                        ['target_value', $request->user()->niveau]
                    ])
                    ->orWhere([
                        ['target_type', 'groupe'],
                        ['target_value', $request->user()->groupe]
                    ]);
            })
            ->with('targets')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notifications);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'targets' => 'required|array',
            'targets.*.type' => 'required|in:level,filiere,groupe,all',
            'targets.*.value' => 'nullable|string'
        ]);

        $notification = $request->user()->notifications()->create([
            'message' => $validated['message']
        ]);

        foreach ($validated['targets'] as $target) {
            $notification->targets()->create([
                'target_type' => $target['type'],
                'target_value' => $target['type'] === 'all' ? null : $target['value']
            ]);
        }

        return response()->json($notification, 201);
    }

    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();
        
        return response()->json(null, 204);
    }
    public function markAsRead($id)
{
    $notification = Notification::findOrFail($id);
    $notification->update(['read_at' => now()]);
    
    return response()->json(['success' => true]);
}
}