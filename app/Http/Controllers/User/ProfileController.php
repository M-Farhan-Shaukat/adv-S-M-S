<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        return view('user.profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'cnic' => 'required|string|max:20',
        ];

        // If change password switch is ON
        if ($request->change_password) {

            $rules['current_password'] = 'required';
            $rules['password'] = 'required|min:8|confirmed';

            $request->validate($rules);

            // check current password
            if (!Hash::check($request->current_password, $user->password)) {

                return response()->json([
                    'errors' => [
                        'current_password' => ['Current password is incorrect']
                    ]
                ], 422);

            }

        } else {
            $request->validate($rules);
        }

        // update profile
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'cnic' => $request->cnic,
        ]);

        // update password
        if ($request->change_password) {

            $user->update([
                'password' => Hash::make($request->password)
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully'
        ]);
    }

}
