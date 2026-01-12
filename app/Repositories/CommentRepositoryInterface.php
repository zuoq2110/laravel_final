<?php

namespace App\Repositories;

use App\Models\Comment;

interface CommentRepositoryInterface
{
    public function create(array $data): Comment;
}