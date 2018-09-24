<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    protected $fillable = ['name', 'method_as', 'method_from', 'method_to', 'unit', 'measure_semantic', 'entity_semantic', 'creator', 'confirmed', 'username'];

}
