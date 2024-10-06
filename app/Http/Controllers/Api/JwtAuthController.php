<?php

namespace App\Http\Controllers\Api;

use App\DTO\LoginDTO;
use App\DTO\RegisterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class JwtAuthController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), Response::HTTP_BAD_REQUEST);
        }

        $registerDTO = new RegisterDTO(
            $request->input('name'),
            $request->input('email'),
            $request->input('password')
        );

        $result = $this->userService->createUser($registerDTO);

        return response()->json($result, Response::HTTP_CREATED);
    }

    public function login(LoginRequest $request)
    {
        $loginDTO = new LoginDTO(
            $request->input('email'),
            $request->input('password')
        );

        $result = $this->userService->login($loginDTO);

        if (! $result['success']) {
            return response()->json(['error' => $result['message']], $result['status']);
        }

        return response()->json($result['data'], $result['status']);
    }

    public function getUser()
    {
        $result = $this->userService->getUser();

        if (! $result['success']) {
            return response()->json(['error' => $result['message']], $result['status']);
        }

        return response()->json($result['data'], $result['status']);
    }

    public function logout()
    {
        $result = $this->userService->logout();

        return response()->json(['message' => $result['message']], $result['status']);
    }
}
