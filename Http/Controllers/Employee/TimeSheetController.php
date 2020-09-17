<?php

namespace App\Http\Controllers\Employee;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libraries\Access;
use Illuminate\Support\Facades\Hash;

class TimeSheetController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!Access::employeecheckUserAccess())
        {
            return redirect('admin/home');
        }

        // filters 
        $where = [];
        if(\Auth::user()->user_type=='A')  
        {
           
        }else{
            $where['userId'] = \Auth::user()->id;
        }
       

        $timesheets = \App\Models\TimeSheet::with('user_details')->where($where)->paginate(config('wallet.resultsPerPage'));

        return view('admin.employee.timesheet.index', ['timesheets' => $timesheets]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Access::employeecheckUserAccess())
        {
            return redirect('admin/home');
        }
 
        return view('admin.employee.timesheet.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Access::employeecheckUserAccess())
        {
            return redirect('admin/home');
        }
        
        $rules = [           
            'duration' => 'required',     
            'fromDate' => 'unique:employeetimesheet,fromDate,NULL,timeSheetId,userId,'.\Auth::user()->id      
        ];

        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {

            $otherDocumentpath ='';     
       
            if ($request->hasFile('document')) {
                // Get filename with the extension
                $filenameWithExt = $request->file('document')->getClientOriginalName();
                //Get just filename
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                // Get just ext
                $extension = $request->file('document')->getClientOriginalExtension();
                // Filename to store
                $otherDocumentpath = $filename . '_' . time() . '.' . $extension;
                // Upload Image
                $path = $request->file('document')->storeAs('uploads/employeedocument', $otherDocumentpath);
            }
           
            $user = new \App\Models\TimeSheet();                  
            if($request->fromDate)
            {           
            $user->fromDate = \Carbon\Carbon::parse($request->fromDate)->format('Y-m-d');         
            }     
            $user->duration = $request->duration;           
            if($request->assignment){
                $user->assignment = $request->assignment;
            }                    
            if($request->note){
                $user->note = $request->note;
            } 
            if($request->serviceCode){
                $user->serviceCode = $request->serviceCode;
            }               
            if ($otherDocumentpath) {
                $user->document = $otherDocumentpath;
            }                 
           
            $user->userId = \Auth::user()->id;           
            $user->created_at = date('Y-m-d H:i:s');
            $user->save();  
            \Session::flash('message', 'TimeSheet Added Successfuly.');
            return redirect('timesheet');
        }
    }

 


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!Access::employeecheckUserAccess())
        {
            return redirect('admin/home');
        }
        $timesheet      = \App\Models\TimeSheet::find($id);
        return view('admin.employee.timesheet.edit', [ 'timesheet' => $timesheet]);
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
            'duration' => 'required',     
        ];
        
        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return redirect()->back()->withInput()->withErrors($validator);
        }
        else
        {
            $otherDocumentpath ='';     
       
            if ($request->hasFile('document')) {
                // Get filename with the extension
                $filenameWithExt = $request->file('document')->getClientOriginalName();
                //Get just filename
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                // Get just ext
                $extension = $request->file('document')->getClientOriginalExtension();
                // Filename to store
                $otherDocumentpath = $filename . '_' . time() . '.' . $extension;
                // Upload Image
                $path = $request->file('document')->storeAs('uploads/employeedocument', $otherDocumentpath);
            }
            // add user
            $user = \App\Models\TimeSheet::find($id);    
            if($request->fromDate)
            {           
            $user->fromDate = \Carbon\Carbon::parse($request->fromDate)->format('Y-m-d');         
            }     
            $user->duration = $request->duration;           
            if($request->assignment){
                $user->assignment = $request->assignment;
            }                    
            if($request->note){
                $user->note = $request->note;
            } 
            if($request->serviceCode){
                $user->serviceCode = $request->serviceCode;
            } 
            if ($otherDocumentpath) {
                $user->document = $otherDocumentpath;
            }    
             $user->save();           

            \Session::flash('message', 'Timesheet Details Updated Successfully.');
            return redirect('timesheet');
        }
    }

    public function downloadTimeSheet($filename) {

        return response()->download(storage_path("app/uploads/employeedocument/{$filename}"));
    }


}
