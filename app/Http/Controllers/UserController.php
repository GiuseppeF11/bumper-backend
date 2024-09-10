<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function store(Request $request)
    {
        // Validazione dei dati
        $request->validate([
            'nickname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'picture' => 'nullable|string',
            'isAdult' => 'nullable|boolean',
            'location' => 'nullable|string|max:255',
            'interests' => 'nullable|array',
        ]);

        // Aggiornamento o creazione dell'utente
        $user = User::updateOrCreate(
            ['email' => $request->email],
            [
                'nickname' => $request->nickname,
                'picture' => $request->picture,
                'password' => bcrypt('password-default'),
                'isAdult' => $request->isAdult,
                'location' => $request->location,
                'interests' => $request->interests,
            ]
        );

        return response()->json(['message' => 'User saved successfully!', 'user' => $user], 200);
    }

    public function update(Request $request, $nickname)
{
    // Validazione dei dati
    $request->validate([
        'isAdult' => 'nullable|boolean',
        'location' => 'nullable|string|max:255',
        'interests' => 'nullable|array',
    ]);

    // Trova l'utente per nickname
    $user = User::where('nickname', $nickname)->first();
    

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    if ($request->has('interests')) {
        $user->interests = json_encode($request->interests);
    }

    // Aggiorna i campi, se forniti
    $user->isAdult = $request->isAdult ?? $user->isAdult;
    $user->location = $request->location ?? $user->location;

    // Salvataggio delle modifiche
    $user->save();

    return response()->json(['message' => 'User updated successfully!', 'user' => $user], 200);
}

}
