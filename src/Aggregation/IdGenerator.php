<?php

namespace App\Aggregation;

final class IdGenerator
{
    public function generate(): string
    {
        return bin2hex(random_bytes(12));
    }
}
