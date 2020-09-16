<?php

namespace App\Libraries;

class Access
{

    public static function checkUserAccess()
    {
        // allowed admin level 
        $admin    = ['A'];
        $userType = \Auth::user()->user_type;        
        return in_array($userType, $admin);
    }
  
    public static function employeecheckUserAccess()
    {
        // allowed admin level 
        $admin    = ['E','A'];
        $userType = \Auth::user()->user_type;        
        return in_array($userType, $admin);
    }

}
