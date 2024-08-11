<?php

namespace App\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;



class Guest extends Model
{

    protected $table = 'guest';
    protected $primaryKey = 'id';

    protected $fillable = ['first_name', 'last_name', 'phone', 'email', 'country'];
}