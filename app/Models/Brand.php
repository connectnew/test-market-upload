<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\Relation;

class Brand extends Model
{
    use SoftDeletes;

    protected $table = 'brands';

    protected $guarded = ['id'];

    /**
     * START Relations
     */
    public function products(): Relation
    {
        return $this->hasMany(Product::class, 'brand_id');
    }
    /**
     * END Relations
     */
}
