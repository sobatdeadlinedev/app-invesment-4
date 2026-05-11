<?php

namespace App\Http\Controllers\Member;

use App\Models\User;
use App\Http\Controllers\Controller;

class AccessController extends Controller
{
    public function index()
    {
        $user = User::current();
        return view('member.pages.access.index', compact('user'));
    }
}
