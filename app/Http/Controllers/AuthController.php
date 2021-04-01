<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class AuthController extends Controller {

    const TABLE = 'user';

    /**
     * Register a new user.
     *
     * @param  Request  $request
     * @return Response
     */
    public function register (Request $request) {
        //validate incoming request 
        $this->validate($request, [
            'name' => 'required|string',
            'email' => 'required|email|unique:user',
            'password' => 'required|confirmed',
        ]);

        try {

            $user = new User();
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $plainPassword = $request->input('password');
            $user->password = app('hash')->make($plainPassword);
            $user->save();

            return new Response('', 200);

        } catch (\Exception $e) {
            return new Response('', 500);
        }
    }

    public function login (Request $request) {
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        $credentials = $request->only(['email', 'password']);

        if (!$token = Auth::attempt($credentials)) {
            return new Response('Unauthorized', 401);
        }

        return new Response($this->getTokenResponse($token));
    }

    protected function getTokenResponse($token) {
        return [
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ];
    }
}