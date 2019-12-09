<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;

use App\UseCases\AdminCase;

class AuthController extends Controller
{
    private $adminCase;

    public function __construct(AdminCase $adminCase)
    {
        $this->adminCase = $adminCase;
    }

    public function getLogin()
    {
        return view('auth.login');
    }

    public function postLogin(LoginRequest $request)
    {
        $authData = [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'active' => 1
        ];

        if (!$this->adminCase->auth($authData))
        {
            return response()->json([
                'functions' => [
                    'showAlert' => [
                        'params' => [
                            'alert_type' => 'error',
                            'alert_header' => 'ошибка',
                            'alert_message' => 'Логин или пароль неверны или админ не активирован'
                        ]
                    ],
                ]
            ], 403);
        }


        return response()->json([
            'functions' => [
                'redirect' => [
                    'params' => [
                        'url' => route('admin'),
                    ]
                ],
            ]
        ]);
    }

    public function logout() {
        Auth::guard( 'admins' )->logout();
        return redirect()->route( 'admin.get.login' );
    }
}
