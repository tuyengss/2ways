<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sms extends Model
{
    public $table = "logs";
    protected $fillable = ['sender', 'reciever', 'content', 'status', 'updated_at'];

}
