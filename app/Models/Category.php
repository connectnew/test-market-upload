<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\Relation;
use Kalnoy\Nestedset\NodeTrait;

class Category extends Model
{
    use SoftDeletes, NodeTrait;

    protected $table = 'categories';

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
