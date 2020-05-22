<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;

use App\UseCases\AdminCase;
use Illuminate\View\View;

/**
 * Class AuthController
 * @package App\Http\Controllers
 */
class AuthController extends Controller
{
    private $adminCase;

    /**
     * AuthController constructor.
     * @param AdminCase $adminCase
     */
    public function __construct(AdminCase $adminCase)
    {
        $this->adminCase = $adminCase;
    }

    /**
     * @return Factory|View
     */
    public function getLogin()
    {
        return view('auth.login');
    }

    /**
     * @param LoginRequest $request
     * @return JsonResponse
     */
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

    /**
     * @return RedirectResponse
     */
    public function logout() {
        Auth::guard( 'admins' )->logout();
        return redirect()->route( 'admin.get.login' );
    }
}
