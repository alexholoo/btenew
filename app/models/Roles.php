<?php

namespace App\Models;

use Phalcon\Mvc\Model;

class Roles extends Model
{
    const ADMIN     = 1;
    const PURCHASE  = 2;
    const MARKETING = 3;
    const WAREHOUSE = 4;
    const SHIPMENT  = 5;
    const RMA       = 6;
    const CSR       = 7;
    const USER      = 8;
    const GUEST     = 9;
}
