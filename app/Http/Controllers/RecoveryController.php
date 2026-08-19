<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RecoveryController extends Controller
{
    // 1. Show the form to enter Email/Username
    public function showFindAccount()
    {
        return view('auth.recover', ['step' => 1]);
    }

    // 2. Process Email/Username -> Show Security Question
    public function findAccount(Request $request)
    {
        $request->validate(['identifier' => 'required|string']);
        
        $user = User::where('email', $request->identifier)
                    ->orWhere('username', $request->identifier)
                    ->first();

        if (!$user) {
            return back()->withErrors(['identifier' => 'No account found with this email or username.'])->withInput();
        }

        if (!$user->security_question || !$user->security_answer) {
            return back()->withErrors(['identifier' => 'This account does not have security questions set up. Please contact the system administrator.'])->withInput();
        }

        return view('auth.recover', [
            'step' => 2,
            'user_id' => $user->id,
            'question' => $user->security_question
        ]);
    }

    // 3. Verify Security Answer -> Show Reset Form
    public function checkAnswer(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'answer' => 'required|string'
        ]);

        $user = User::find($request->user_id);

        if (strtolower($user->security_answer) !== strtolower($request->answer)) {
            return back()->withErrors(['answer' => 'The security answer is incorrect.'])->withInput([
                'user_id' => $user->id, 
                'question' => $user->security_question
            ]);
        }

        return view('auth.recover', [
            'step' => 3,
            'user_id' => $user->id
        ]);
    }

    // 4. Reset Password -> Redirect to Login
    public function resetAccount(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'password' => 'required|string|min:8|confirmed'
        ]);

        $user = User::find($request->user_id);
        
        // Your User model automatically hashes this password!
        $user->password = $request->password; 
        $user->save();

        return redirect()->route('login')->with('status', 'Password reset successfully! You can now log in with your new password.');
    }
}