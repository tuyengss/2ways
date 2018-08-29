<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mo extends Model
{
    public $table = "gateway_logs";
    protected $fillable = ['Username','Phonenumber', 'MsgContent', 'status', 'RequestId', 'updated_at'];
}
