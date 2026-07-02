<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DescriptorBase extends Model
{
	/**
     * The attributes that should protected.
     *
     * @var array
     */
    protected $guarded = [];

	/**
     * Inter model relationship
     * 
     */
    public function descriptor() {
        return $this->belongsTo(Descriptor::class);
    }
}
