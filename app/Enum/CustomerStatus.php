<?php



namespace App\Enum;


/**
 * Class CustomerStatus
 *
 * @author
 * @package App\Enums
 */
enum CustomerStatus: string
{
    case Active = 'active';
    case Disabled = 'disabled';

}
