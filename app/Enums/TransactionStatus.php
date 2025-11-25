<?php

namespace App\Enums;

enum TransactionStatus: string
{
    case PENDING = 'pending';
    case AUTHORIZED = 'authorized';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
}
