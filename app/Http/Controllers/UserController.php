<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', User::class);

        $usersWithNoPhone = User::where('phone', '==', null)->get();

        if($usersWithNoPhone->isEmpty()){
            return response()->json([
                "message" => "No hay usuarios sin teléfono registrado.",
            ]);
        }

        return new UserResource($usersWithNoPhone);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {

        $this->authorize('view', $user);

        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {

        $this->authorize('update', $user);

        $user->update($request->validated());

        return new UserResource($user);

    }

    public function updatePassword(UpdatePasswordRequest $request, User $user)
    {
        $user = User::findOrFail($user->id);

        $this->authorize('update', $user);

        if (!Hash::check($request->current_password, $user->password)) {

            Log::error('Fallo en el cambio de contraseña', [
                'user_id' => $user->id,
                'password_attempt' => $request->current_password,
                'IP' => $request->ip(),
                'Method' => $request->method(),
                'Browser' => $request->userAgent(),
            ]);

            return response()->json([
                'message' => 'La contraseña actual es incorrecta.',
            ], 422);
        }

        $user->password = $request->password;
        $user->save();

        $user->tokens()->delete();
        $newToken = $user->createToken('api-token')->accessToken;

        Log::info('Cambio de contraseña', [
            'user_id' => $user->id,
        ]);

        return response()->json([
            'message' => 'Contraseña cambiada correctamente.',
            'token' => $newToken,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
