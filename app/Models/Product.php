<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class Product extends Model
{
    protected $table = 'products';

    protected $guarded = ['id'];

    /**
     * START Relations
     */
    public function brand(): Relation
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function category(): Relation
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    /**
     * END Relations
     */
}
