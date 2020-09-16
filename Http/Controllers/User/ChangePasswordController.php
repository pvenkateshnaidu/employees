<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{

    public function changePassword()
    {
        return view('admin.users.change-password', ['id' => \Auth::user()->id]);
    }

    public function updatePassword(Request $request, $id)
    {
        $rules = [
            'current_pass' => 'required|string',
            'new_pass'     => 'required|string|password',
            'confirm_pass' => 'required|same:new_pass',
        ];

        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return redirect()->back()->withInput()->withErrors($validator);
        }
        else
        {
            $user           = \App\Models\User::find($id);
            $user->password = Hash::make($request->new_pass);
            $user->save();
            
            \Session::flash('message', 'Password changed Successfully.');
            return redirect('admin/home');
            
        }
    }

}
