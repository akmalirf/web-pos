<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name','price_forSale','price_fromSupplier','image','category_id','supplier_id','stock','profit'];


    public function categories()
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function order_details()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
