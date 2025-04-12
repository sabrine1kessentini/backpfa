<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        // Récupère les notifications de l'utilisateur
        return $request->user()->notifications()
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'targets' => 'required|array',
            'targets.*.type' => 'required|in:level,filiere,all',
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
}