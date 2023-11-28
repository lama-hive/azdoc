<?php

declare(strict_types=1);

namespace Lamahive\Azdoc\Adapter\Model\Command;

use Exception;
use Lamahive\Azdoc\Adapter\Azdo;
use Lamahive\Azdoc\Adapter\Factory\WorkItemFactory;
use Lamahive\Azdoc\Adapter\Model\WorkItem;
use function is_array;
use function json_decode;

class GetAzdoTasksByQueryId extends AbstractGet
{
    protected Azdo $azdo;
    protected WorkItemFactory $workItemFactory;

    /**
     * @param Azdo $azdo
     * @param WorkItemFactory $workItemFactory
     */
    public function __construct(Azdo $azdo, WorkItemFactory $workItemFactory)
    {
        parent::__construct($azdo);
        $this->workItemFactory = $workItemFactory;
    }

    /**
     * @param string $queryId
     * @return WorkItem[]
     * @throws Exception
     */
    public function execute(string $queryId): array
    {
        $urls = $this->loadWorkItemUrls($queryId);

        $workItems = [];
        foreach ($urls as $workItemId => $url) {
            $workItem = $this->workItemFactory->createFromUrl($url);

            $workItems[$workItemId] = $workItem;
        }

        return $workItems;
    }

    /**
     * @param string $queryId
     * @return array
     */
    protected function loadWorkItemUrls(string $queryId): array
    {
        $queryResult = json_decode($this->azdo->getWiql($queryId), true);

        $urls = [];

        if (!is_array($queryResult)) {
            return $urls;
        }

        foreach ($queryResult['workItems'] as $workItem) {
            $urls[$workItem['id']] = $workItem['url'];
        }

        return $urls;
    }
}
