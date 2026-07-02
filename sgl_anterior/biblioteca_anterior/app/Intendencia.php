<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Intendencia extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    /**
     * The attributes that should protected.
     *
     * @var array
     */
    protected $guarded = [];
}
