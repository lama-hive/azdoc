<?php

declare(strict_types=1);

namespace Lamahive\Azdoc\Adapter\Model\Command;

use Exception;
use Lamahive\Azdoc\Adapter\Azdo;
use Lamahive\Azdoc\Adapter\Factory\WorkItemFactory;
use Lamahive\Azdoc\Adapter\Model\WorkItem;
use function str_replace;
use function implode;
use function json_decode;

class GetAzdoTasksByQueryIdBulk extends GetAzdoTasksByQueryId
{
    public function __construct(Azdo $azdo, WorkItemFactory $workItemFactory)
    {
        parent::__construct($azdo, $workItemFactory);
    }

    /**
     * @param string $queryId
     * @return array|WorkItem[]
     * @throws Exception
     */
    public function execute(string $queryId): array
    {
        $urls = $this->loadWorkItemUrls($queryId);

        $ids = array_keys($urls);
        $fields = [
            'System.Id',
            'Microsoft.VSTS.Common.Priority',
            'System.State',
            'System.AssignedTo',
            'System.CreatedBy',
            'System.Description',
            'System.CreatedDate',
            'System.Tags',
            'System.Title'
        ];
        $fields = [];

        $workItemsApiResponse = $this->azdo->getWorkItems($this->buildWorkItemsQuery($ids, $fields));

        $workItemsJson = json_decode($workItemsApiResponse, false);

        $workItems = [];
        foreach ($workItemsJson->value as $workItemJson) {
            $workItem = $this->workItemFactory->createFromJson($workItemJson);

            $workItems[$workItemJson->id] = $workItem;
        }

        return $workItems;
    }

    /**
     * @param int[] $workItemsIds
     * @param string[] $fields
     * @return string
     */
    protected function buildWorkItemsQuery(array $workItemsIds, array $fields): string
    {
        return str_replace(
            [
                '{{workItemsIds}}'
            ],
            [
                implode(',', $workItemsIds)
            ],
            'ids={{workItemsIds}}'
        );
    }
}
