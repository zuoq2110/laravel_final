<?php

namespace App\Repositories;

use App\Models\Log;

interface LogRepositoryInterface
{
    public function create(array $data): Log;
}