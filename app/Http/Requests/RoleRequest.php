<?php

namespace App\Http\Requests;

use App\Domain\User\Models\User;
use App\Models\OwnerInterface;
use Webmozart\Assert\Assert;

abstract class RoleRequest extends BaseRequest
{
    protected function isSelf(): bool
    {
        /** @var User|null $authenticatedUser */
        $authenticatedUser = $this->user();

        if ($authenticatedUser === null) {
            return false;
        }

        /** @var array<string, mixed> $parameters */
        $parameters = $this->route()?->parameters();
        Assert::notEmpty($parameters);

        foreach ($parameters as $parameter) {
            if (!$parameter instanceof OwnerInterface) {
                throw new \LogicException(\get_class($parameter) . ' does not implement OwnerInterface');
            }

            $owner = $parameter->getOwner();

            if ($owner === null || $owner->id !== $authenticatedUser->id) {
                return false;
            }
        }

        return true;
    }
}
