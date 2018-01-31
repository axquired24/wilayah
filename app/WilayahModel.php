<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WilayahModel extends Model
{
    protected $table = 'wilayah';
    protected $primaryKey = 'kode';
    public $incrementing = false;
    public $timestamps = false;
}
