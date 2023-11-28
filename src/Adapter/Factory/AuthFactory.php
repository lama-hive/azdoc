<?php

declare(strict_types=1);

namespace Lamahive\Azdoc\Adapter\Factory;

use Lamahive\Azdoc\Adapter\Model\Auth;

class AuthFactory
{
    public function create(array $data): Auth
    {
        return new Auth(...$data);
    }
}
