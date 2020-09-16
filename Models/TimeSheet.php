<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;


class TimeSheet extends Model
{
	protected $table = 'employeetimesheet';
	protected $primaryKey = 'timeSheetId';

	protected $fillable = [
		'duration'		
	];
    public function user_details()
    {
        return $this->belongsTo(User::class, 'userId');
    }
	
}
