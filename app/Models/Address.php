<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 
        'name', 
        'phone', 
        //Address      
        'zipcode', 'street', 'number', 'complement', 'neighborhood', 'state', 'city',
    ];

    /**
     * Relationships
    */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
