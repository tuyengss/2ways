<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class keyword  extends Model
{
    public $table = "keywords";
    protected $fillable = ['keyword', 'content', 'sender', 'updated_at'];
}
