<?php

namespace App\Services;

use App\DTO\LoginDTO;
use App\DTO\RegisterDTO;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserService
{
    public function createUser(RegisterDTO $registerDTO): array
    {
        $user = User::create([
            'name' => $registerDTO->name,
            'email' => $registerDTO->email,
            'password' => Hash::make($registerDTO->password),
        ]);

        $token = JWTAuth::fromUser($user);

        return ['user' => $user, 'token' => $token];
    }

    public function login(LoginDTO $loginDTO): array
    {
        $credentials = [
            'email' => $loginDTO->email,
            'password' => $loginDTO->password,
        ];

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return [
                    'success' => false,
                    'message' => 'Invalid credentials',
                    'status' => Response::HTTP_UNAUTHORIZED,
                ];
            }

            return [
                'success' => true,
                'data' => ['token' => $token],
                'status' => Response::HTTP_OK,
            ];
        } catch (JWTException $e) {
            return [
                'success' => false,
                'message' => 'Could not create token',
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ];
        }
    }

    public function getUser(): array
    {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return [
                    'success' => false,
                    'message' => 'User not found',
                    'status' => Response::HTTP_NOT_FOUND,
                ];
            }

            return [
                'success' => true,
                'data' => ['user' => $user],
                'status' => Response::HTTP_OK,
            ];
        } catch (JWTException $e) {
            return [
                'success' => false,
                'message' => 'Invalid token',
                'status' => Response::HTTP_BAD_REQUEST,
            ];
        }
    }

    public function logout(): array
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return [
            'success' => true,
            'message' => 'Successfully logged out',
            'status' => Response::HTTP_OK,
        ];
    }
}
