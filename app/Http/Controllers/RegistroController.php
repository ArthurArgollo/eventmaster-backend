<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegistroController extends Controller
{
    public function salvar(Request $request) 
    {
        $request->validate([
            'name'     => 'required',
            'email'    => 'required|email|unique:users',
            'cpf'      => 'required|unique:users',
            'password' => 'required|min:6' 
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'cpf'      => $request->cpf,
            'password' => Hash::make($request->password), 
        ]);

        return back()->with('sucesso', 'Membro da associação registrado!');
    }
}