<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTAuthController extends Controller
{
    // Метод для регистрации нового пользователя
    public function register(Request $request)
    {
        // Валидация входных данных
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Возвращаем ошибку, если валидация не прошла
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        // Создаем пользователя
        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        // Генерируем JWT-токен для пользователя
        $token = JWTAuth::fromUser($user);

        // Возвращаем данные пользователя и токен
        return response()->json(compact('user', 'token'), 201);
    }

    // Метод для входа пользователя в систему
    public function login(Request $request)
    {
        // Получаем учетные данные из запроса
        $credentials = $request->only('email', 'password');

        try {
            // Попытка аутентифицировать и получить токен
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Неверные учетные данные'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Не удалось создать токен'], 500);
        }

        // Возвращаем сгенерированный токен
        return response()->json(compact('token'));
    }

    // Метод для получения данных аутентифицированного пользователя
    public function getUser()
    {
        try {
            // Получаем текущего пользователя по токену
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['error' => 'Пользователь не найден'], 404);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Недействительный токен'], 400);
        }

        // Возвращаем данные пользователя
        return response()->json(compact('user'));
    }

    // Метод для выхода из системы
    public function logout()
    {
        // Инвалидируем токен, чтобы пользователь больше не мог использовать его
        JWTAuth::invalidate(JWTAuth::getToken());

        // Возвращаем подтверждение
        return response()->json(['message' => 'Успешный выход из системы']);
    }
}
