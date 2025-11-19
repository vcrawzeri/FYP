<?php



namespace App\Enum;


/**
 * Class AddressType
 *
 * @author
 * @package App\Enums
 */
enum TypeAddress: string
{
    case Shipping = 'shipping';
    case Billing = 'billing';
}
