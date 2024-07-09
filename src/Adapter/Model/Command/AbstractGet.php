<?php

declare(strict_types=1);

namespace Lamahive\Azdoc\Adapter\Model\Command;

use Lamahive\Azdoc\Adapter\Azdo;

class AbstractGet
{
    protected Azdo $azdo;

    public function __construct(Azdo $azdo)
    {
        $this->azdo = $azdo;
    }

    public function azdo(): Azdo
    {
        return $this->azdo;
    }
}
