<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    protected $fillable = ['name', 'method_from', 'method_to', 'unit', 'semantics', 'creator'];

}
