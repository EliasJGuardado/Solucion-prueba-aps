<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Asociado extends Model
{
    protected $fillable=['nombre','dui','nit','direccion','estado'];
}
