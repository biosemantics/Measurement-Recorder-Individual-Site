<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActionLog extends Model
{
    //
    protected $fillable = ['user_id', 'action_type', 'model_id'];
}
