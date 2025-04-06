<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Reminder;
use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\ReminderNotification;

class ReminderController extends Controller
{
    public function index()
    {
        return response()->json(Reminder::where('user_id', auth()->id())->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplement_id' => 'required|exists:supplements,id',
            'time' => 'required|string',
        ]);

        $reminder = Reminder::create([
            'user_id' => auth()->id(),
            'supplement_id' => $request->supplement_id,
            'time' => $request->time,
        ]);

        // Send Push Notification
        $user = User::find(auth()->id());
        if ($user->device_token) {
            $user->notify(new ReminderNotification($reminder));
        }

        return response()->json(['message' => 'Reminder added successfully']);
    }

    public function destroy($id)
    {
        $reminder = Reminder::where('id', $id)->where('user_id', auth()->id())->first();

        if (!$reminder) {
            return response()->json(['message' => 'Reminder not found'], 404);
        }

        $reminder->delete();
        return response()->json(['message' => 'Reminder deleted']);
    }
}

