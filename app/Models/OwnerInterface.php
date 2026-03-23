<?php

namespace App\Models;

use App\Domain\User\Models\User;

interface OwnerInterface
{
    public function getOwner(): ?User;
}
