<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Requests\Api\LoginReuqest;
use App\Http\Requests\Api\RegisterReuqest;

class AuthController extends Controller
{
    //Создаем функцию register
    // создаем  юзера
    // берем данные из запроса, пароль хэшируем 
    // отправляем ответ 
    public function register(RegisterReuqest $request)
    {

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user'
        ]);

        return response()->json([
            'success' => true
        ], 201);
    }

    // создаем функцию логин 
    // делаем проверку есть ли такие данные в базе, если нет то выдаем сообщение об ошибке по тз
    // получем текущего авторизироввнного пользователя и создаем ему новый токен 
    // отправляем ответ по тз 

    public function login(LoginReuqest $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'massage' => 'Invalid data',
                'errors' => [
                    'email' => ['Invalid data']
                ]
            ], 422);
        }
        /** @var User $user */
        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token
        ], 200);
    }

    // создаем функцию выхода 
    // удаляем токен 
    // возвращаем ответ

    public function logout(Request $request)
    {
        // Метод currentAccessToken() в контексте Laravel Sanctum возвращает токен,
        // который использовался текущим пользователем при создании запроса. 
        $request->user()->currentAccessToken()->delete();

        return response()->json(['massage' => 'Logged out'], 200);
    }
}
