<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Twoway extends Model
{
    public $table = "2ways_logs";
    protected $fillable = ['sim','port', 'type', 'msgcontent', 'status', 'from', 'to', 'updated_at'];
}
