<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HomeController extends Controller
{
    //

    public function index()
    {
        return view('home');
    }

    public function checkDomain($planType)
    {
        if ($planType === 'free' || 'basic' || 'premium') {
            return view('check-domain', ['planType' => $planType]);
        }
        return redirect()->route('home');
    }

    public function checkAvailability(Request $request)
    {
        $data = $request->validate([
            'subdomain' => ['required', 'alpha_num', 'min:3', Rule::unique('users', 'subdomain')],
            'planType' => ['required', 'string', 'max:255']
        ]);

        if ($data) {
            return redirect()->route('auth.create', ['subdomain' => $data['subdomain'], 'planType' => $data['planType']]);
        } else {
            return back()->withErrors('subdomain', 'The subdomain is already taken');
        }
    }
}
