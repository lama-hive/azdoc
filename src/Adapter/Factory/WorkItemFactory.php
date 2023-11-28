<?php

declare(strict_types=1);

namespace Lamahive\Azdoc\Adapter\Factory;

use Exception;
use Lamahive\Azdoc\Adapter\Azdo;
use Lamahive\Azdoc\Adapter\Model\WorkItem;
use stdClass;

class WorkItemFactory
{
    protected Azdo $azdo;
    protected WorkItem $workItem;

    /**
     * @param Azdo $azdo
     * @param WorkItem $workItem
     */
    public function __construct(Azdo $azdo, WorkItem $workItem)
    {
        $this->azdo = $azdo;
        $this->workItem = $workItem;
    }

    public function create(): WorkItem
    {
        return new WorkItem($this->azdo);
    }

    /**
     * @throws Exception
     */
    public function createFromJson(stdClass $json): WorkItem
    {
        $workItem = $this->create();
        $workItem->loadBaseWorkItemJson($json);

        return $workItem;
    }

    /**
     * @throws Exception
     */
    public function createFromUrl(string $url): WorkItem
    {
        $baseWorkItemJson = json_decode($this->azdo->get($url), false);

        $workItem = $this->create();
        $workItem->loadBaseWorkItemJson($baseWorkItemJson);

        return $workItem;
    }
}
