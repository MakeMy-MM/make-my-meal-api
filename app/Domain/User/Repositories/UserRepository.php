<?php

namespace App\Domain\User\Repositories;

use App\Domain\User\Models\User;
use App\DTOs\BaseFieldDTO;
use App\Repositories\ModelRepository;
use Illuminate\Database\Eloquent\Collection;

/**
 * @extends ModelRepository<User>
 *
 * @method Collection<int, User> findByFields(BaseFieldDTO $data, array<string> $with = [], array<string, string> $orderBy = [])
 * @method User|null              findFirstByFields(BaseFieldDTO $data, array<string> $with = [], array<string, string> $orderBy = [])
 */
class UserRepository extends ModelRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function create(BaseFieldDTO $dto): User
    {
        return User::create($dto->toArray());
    }
}
