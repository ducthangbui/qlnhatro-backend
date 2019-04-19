<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hostel extends Model
{
    protected $fillable = [
      'userid', 'electricprice', 'waterprice', 'sanitationcost', 'securitycost', 'closedtime', 'status',
        'price', 'img', 'addid', 'haslandlords'
    ];
}
