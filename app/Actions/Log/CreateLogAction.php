<?php

namespace App\Actions\Log;

use App\Repositories\LogRepositoryInterface;

class CreateLogAction
{
    public function __construct(
        private LogRepositoryInterface $logRepository
    ){}
    
    public function execute(array $data){
        return $this->logRepository->create($data);
    }
}