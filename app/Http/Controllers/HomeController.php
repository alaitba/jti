<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

/**
 * Class HomeController
 * @package App\Http\Controllers
 */
class HomeController extends Controller
{

    /**
     * @return RedirectResponse
     */
    public function index()
    {
        //TODO custom redirect for different roles
        return redirect()->route('admin.admins');
        //return view('home.index');
    }
}
