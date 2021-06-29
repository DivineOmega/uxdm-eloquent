<?php

namespace DivineOmega\uxdm\TestClasses\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SoftDeletableUser extends Model
{
    use SoftDeletes;

    public $primaryKey = 'id';
    public $timestamps = false;
}
