<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Domain\Auth\Enums\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

/**
 * @OA\Tag(name="Auth", description="Authentication endpoints")
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(path="/api/v1/auth/register", tags={"Auth"}, summary="Register user",
     *   @OA\RequestBody(required=true, @OA\JsonContent(
     *     required={"name","email","password","role"},
     *     @OA\Property(property="name", type="string"),
     *     @OA\Property(property="email", type="string", format="email"),
     *     @OA\Property(property="password", type="string", minLength=8),
     *     @OA\Property(property="role", type="string", enum={"admin","seller","financial"})
     *   )),
     *   @OA\Response(response=201, description="User registered")
     * )
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole(Role::from($request->role)->value);

        $token = auth('api')->login($user);

        return response()->json([
            'data' => [
                'user' => $user->only('id', 'name', 'email'),
                'token' => $token,
                'token_type' => 'bearer',
            ],
        ], 201);
    }

    /**
     * @OA\Post(path="/api/v1/auth/login", tags={"Auth"}, summary="Login",
     *   @OA\RequestBody(required=true, @OA\JsonContent(
     *     required={"email","password"},
     *     @OA\Property(property="email", type="string", format="email"),
     *     @OA\Property(property="password", type="string")
     *   )),
     *   @OA\Response(response=200, description="Token returned"),
     *   @OA\Response(response=401, description="Invalid credentials")
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $token = auth('api')->attempt($request->only('email', 'password'));

        if (!$token) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'data' => [
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60,
            ],
        ]);
    }

    public function me(): JsonResponse
    {
        return response()->json(['data' => auth('api')->user()]);
    }

    public function logout(): JsonResponse
    {
        auth('api')->logout();
        return response()->json(['message' => 'Logged out']);
    }
}
