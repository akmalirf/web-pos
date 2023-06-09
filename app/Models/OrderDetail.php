<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'product_id', 'total_price', 'amount_of_item','profit'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return$this->belongsTo(Product::class);
    }

}
