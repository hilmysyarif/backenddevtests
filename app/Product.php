<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Product extends Eloquent
{
    protected $connection = 'mongodb';

    protected $primaryKey = '_product_id';

    /**
     * @var string
     */
    protected $table = 'products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        '_product_id', 'product_name', 'price', 'stock', 'category_id',
    ];


    /**
     * @var array
     */
    protected $guarded = [];

    // Product -> Category
    // foreign_key : category_id
    //
    public function category()
    {
        $this->belongsTo(Category::class, 'category_id');
    }
}
