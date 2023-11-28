<?php

declare(strict_types=1);

namespace Lamahive\Azdoc\Adapter\Model\Command;

use Lamahive\Azdoc\Adapter\Azdo;
use function json_decode;

class GetQueriesByFilter extends AbstractGet
{
    public function execute(string $filter): array
    {
        return json_decode($this->azdo->getAuto(Azdo::QUERIES_URL, ['{{filter}}' => $filter]), true);
    }

    public function getCount(string $filter): int
    {
        $queryResult = $this->execute($filter);

        return (int) $queryResult['count'];
    }
}
