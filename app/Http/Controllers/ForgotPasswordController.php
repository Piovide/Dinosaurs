<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', 'Se l\'email è registrata, riceverai un link per reimpostare la password.');
        }

        // Always return the same message to avoid email enumeration
        return back()->with('success', 'Se l\'email è registrata, riceverai un link per reimpostare la password.');
    }
}
