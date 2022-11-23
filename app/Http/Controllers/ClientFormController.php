<?php

namespace App\Http\Controllers;

use App\Models\ClientForm;
use Illuminate\Http\Request;

class ClientFormController extends Controller
{
    public function store() {
        $data = request()->validate([
            'name' => 'required',
            'email' => 'required|email',
        ]);

        ClientForm::create($data);

        return redirect()->route('client-view');
    }

    public function index() {
        $clientForms = ClientForm::all();

        return view('client-view', ['clients' => $clientForms]);
    }

    public function new() {
        return view('client-app');
    }
}
