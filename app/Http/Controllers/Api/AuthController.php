<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

/**
 * @OA\Info(
 * title="Ejemplo de API",
 * version="1.0.0",
 * description="Este es un ejemplo de documentación de API usando Swagger"
 * )
 *
 * @OA\Server(url="http://127.0.0.1:8000/api", description="Servidor de API local")
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     * path="/register",
     * operationId="register",
     * tags={"Autenticación"},
     * summary="Registrar un nuevo usuario",
     * description="Registra un nuevo usuario con nombre, email y contraseña.",
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"name","email","password","password_confirmation"},
     * @OA\Property(property="name", type="string", example="John Doe"),
     * @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     * @OA\Property(property="password", type="string", format="password", example="password123"),
     * @OA\Property(property="password_confirmation", type="string", format="password", example="password123")
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Usuario registrado exitosamente",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Usuario registrado exitosamente"),
     * @OA\Property(property="user", type="object"),
     * @OA\Property(property="token", type="string", example="token_de_ejemplo")
     * )
     * )
     * )
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Usuario registrado exitosamente',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * @OA\Post(
     * path="/login",
     * operationId="login",
     * tags={"Autenticación"},
     * summary="Iniciar sesión de un usuario",
     * description="Inicia sesión con email y contraseña, y devuelve un token de API.",
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"email","password"},
     * @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     * @OA\Property(property="password", type="string", format="password", example="password123")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Inicio de sesión exitoso",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Inicio de sesión exitoso"),
     * @OA\Property(property="user", type="object"),
     * @OA\Property(property="token", type="string", example="token_de_ejemplo")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Credenciales incorrectas",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Credenciales incorrectas")
     * )
     * )
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        $user = Auth::user();
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * @OA\Post(
     * path="/logout",
     * operationId="logout",
     * tags={"Autenticación"},
     * summary="Cerrar la sesión del usuario",
     * description="Cierra la sesión del usuario autenticado, eliminando su token de API.",
     * security={{"sanctum": {}}},
     * @OA\Response(
     * response=200,
     * description="Cierre de sesión exitoso",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Cierre de sesión exitoso")
     * )
     * )
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Cierre de sesión exitoso'
        ]);
    }
}

