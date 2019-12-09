<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{

    public function index()
    {
        //TODO custom redirect for different roles
        return redirect()->route('admin.admins');
        //return view('home.index');
    }
}
