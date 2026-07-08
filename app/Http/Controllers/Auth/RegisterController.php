<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        // Seed default school for the new user
        \App\Models\School::create([
            'name'        => 'My School',
            'date_format' => 'd/m/Y',
            'gpa_scale'   => '5.0',
        ]);

        // Seed default grade configs
        $grades = [
            ['grade' => 'A+', 'gpa' => 5.00, 'min_percentage' => 80,    'max_percentage' => 100,  'label' => 'Excellent',   'sort_order' => 1],
            ['grade' => 'A',  'gpa' => 4.00, 'min_percentage' => 70,    'max_percentage' => 79.99,'label' => 'Very Good',   'sort_order' => 2],
            ['grade' => 'A-', 'gpa' => 3.50, 'min_percentage' => 60,    'max_percentage' => 69.99,'label' => 'Good',        'sort_order' => 3],
            ['grade' => 'B',  'gpa' => 3.00, 'min_percentage' => 50,    'max_percentage' => 59.99,'label' => 'Satisfactory','sort_order' => 4],
            ['grade' => 'C',  'gpa' => 2.00, 'min_percentage' => 40,    'max_percentage' => 49.99,'label' => 'Average',     'sort_order' => 5],
            ['grade' => 'D',  'gpa' => 1.00, 'min_percentage' => 33,    'max_percentage' => 39.99,'label' => 'Poor',        'sort_order' => 6],
            ['grade' => 'F',  'gpa' => 0.00, 'min_percentage' => 0,     'max_percentage' => 32.99,'label' => 'Fail',        'sort_order' => 7],
        ];
        foreach ($grades as $g) {
            \App\Models\GradeConfig::create($g);
        }

        return redirect()->route('dashboard');
    }
}
