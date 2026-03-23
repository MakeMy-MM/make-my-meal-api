<?php

namespace App\DTOs;

use Illuminate\Database\Eloquent\Model;

interface UpdateDTOInterface
{
    public function getModel(): Model;
}
