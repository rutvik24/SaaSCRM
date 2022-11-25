<?php

namespace App\Http\Controllers;

use App\Models\ClientForm;
use App\Models\User;
use Illuminate\Http\Request;

class ClientFormController extends Controller
{
    public function store()
    {
        $data = request()->validate([
            'name' => 'required',
            'email' => 'required|email',
        ]);


        $domain = request()->getHttpHost();
        $subdomain = explode('.', $domain)[0];

        $domain_user = \DB::connection('mysql2')->table('users')->where('subdomain', $subdomain)->first();

        $client_forms = ClientForm::all();

        if ($domain_user->allowed_data > count($client_forms)) {
            ClientForm::create($data);
            return redirect()->route('client-view');
        } else {
            return back()->withErrors(['error' => 'You have reached the limit of data please upgrade your plan']);
        }
    }

    public function index()
    {
        $clientForms = ClientForm::all();

        return view('client-view', ['clients' => $clientForms]);
    }

    public function new()
    {
        return view('client-app');
    }
}
