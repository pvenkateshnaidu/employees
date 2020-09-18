<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libraries\Access;
use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;
class UserController extends  Controller  
{
   
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!Access::checkUserAccess())
        {
            return redirect('admin/home');
        }

        // filters 
        $where = [];

        // Name fillter
        if ($request->name)
        {
            $where['name'] = $request->name;
        }

        // KTP fillter
        if ($request->email)
        {
            $where['email'] = $request->email;
        }

        // Request status fillter
        if ($request->user_status)
        {
            $where['user_status'] = $request->user_status;
        }

        // Request status fillter
        if ($request->user_type)
        {
            $where['user_type'] = $request->user_type;
        }

     
       
       
        $users = \App\Models\User::select('*')
         ->where($where)->paginate(config('wallet.resultsPerPage'));

        return view('admin.users.index', ['users' => $users]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Access::checkUserAccess())
        {
            return redirect('admin/home');
        }

        $userTypes = config('wallet.userTypes');
        return view('admin.users.create', ['userTypes' => $userTypes]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Access::checkUserAccess())
        {
            return redirect('admin/home');
        }
        
        $rules     = [
            'name'     => 'required|alpha_spaces', 
            'email'    => 'required|email|valid_email|unique:users,email',
            'userType' => 'required|in:A,S,H,U,E',
            'password' => 'required|string|password'
        ];
        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return redirect()->back()->withInput()->withErrors($validator);
        }
        else
        {
            // add user
            $user     = new \App\Models\User();
            $user->name      = $request->name;
            $user->email     = $request->email;
            $user->user_type = $request->userType;
            $user->technologyAssign = $request->technology;
            $user->password  =  Hash::make($request->password);
            $user->save();
                
            \Session::flash('message', 'User Added Successfuly.');
            return redirect('/user');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $userTypes = config('wallet.userTypes');
        $user      = \Auth::user();
        return view('admin.users.edit', ['user' => $user, 'userTypes' => $userTypes]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!Access::checkUserAccess())
        {
            return redirect('admin/home');
        }

        $userTypes = config('wallet.userTypes');
        $user      = \App\Models\User::find($id);
        return view('admin.users.edit', ['user' => $user, 'userTypes' => $userTypes]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules     = [
            'name'     => 'required|alpha_spaces',
            'email'    => 'required|email|valid_email|unique:users,email,' . $id . ',id',         
            'userType' => 'required|in:A,S,H,U,E',
           
        ];
        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return redirect()->back()->withInput()->withErrors($validator);
        }
        else
        {
            // add user
            $user            = \App\Models\User::find($id);
            $user->name      = $request->name;
            $user->email     = $request->email;
            $user->user_type = $request->userType;
            $user->technologyAssign = $request->technology;
            if($request->password)
            $user->password  =  Hash::make($request->password);
            $user->save();
             
            \Session::flash('message', 'User Details Updated Successfully.');
            return redirect('/user');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function updateUserStatus(Request $request)
    {
        $rules     = [
            'id'     => 'required|numeric',
            'status' => 'required|in:A,B',
        ];
        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            \Log::error($validator);
            return array('error' => true, 'msg' => 'Some thing went wrong');
        }
        else
        {
            $user              = \App\Models\User::find($request->id);
            $user->user_status = $request->status;
            $user->save();

            \Log::info('User Id' . $request->id . ' Status Updated to: ' . $request->status);

            return array('error' => false, 'msg' => 'Status updated successfully.');
        }
    }

}
