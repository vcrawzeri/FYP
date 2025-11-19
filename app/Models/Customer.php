<?php

namespace App\Models;

use App\Enum\CustomerStatus;
use App\Enum\TypeAddress;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';

    // Primary key is user_id, not id
    protected $primaryKey = 'user_id';

    // user_id is not auto-incrementing
    public $incrementing = false;

    protected $keyType = 'int';

    protected $fillable = ['user_id', 'first_name', 'last_name', 'phone', 'status'];

    

    public function user()
    {
        // belongsTo makes more sense here, since customer belongs to a user
        return $this->belongsTo(User::class, 'user_id');
    }

    private function _getAddress(): HasOne
    {
        return $this->hasOne(CustomerAddress::class, 'customer_id', 'user_id');
    }

    public function shippingAddress(): HasOne
    {
        return $this->_getAddress()->where('type', '=', TypeAddress::Shipping->value);
    }

    public function billingAddress(): HasOne
    {
        return $this->_getAddress()->where('type', '=', TypeAddress::Billing->value);
    }
}
