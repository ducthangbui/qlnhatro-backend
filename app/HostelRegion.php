<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HostelRegion extends Model
{
    protected $fillable = [
        'regionId', 'hostelid'
    ];
}
