<?php

namespace App;

enum PaymentStatus : string
{
    case Pending = 'pending';
    case Succeeded = 'succeeded';
    case Cancelled = 'cancelled';
}
