<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HomeController extends Controller
{
    //

    public function  index() {
        return view('home');
    }

    public function checkDomain() {
        return view('check-domain');
    }

    public function checkAvailability(Request $request) {
        $data = $request->validate([
            'subdomain' => ['required', 'alpha_num', 'min:3', Rule::unique('users', 'subdomain')]
        ]);

        if ($data) {
            return redirect()->route('auth.create', [ 'subdomain' => $data['subdomain']]);
        } else {
            return back()->withErrors('subdomain', 'The subdomain is already taken');
        }
    }
}
