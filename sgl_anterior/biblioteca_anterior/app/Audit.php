<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
        /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'audits';

    public function getInfo()
    {
        return $this->join('users', 'audits.user_id', '=', 'users.id')
                    ->select('audits.*', 'users.email')
                    ->get();
    }
}
