<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserLog extends Model
{
    //
    protected $fillable = ['user_id', 'action', 'action_detail', 'abnormal_system_response', 'type'];

}
