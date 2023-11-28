<?php

declare(strict_types=1);

namespace Lamahive\Azdoc\Adapter\Model;

use Lamahive\Azdoc\Adapter\Azdo;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use stdClass;
use function explode;
use function trim;
use function array_walk;

class WorkItem
{
    protected ?int $id, $priority;
    protected ?string $state, $title, $assignee, $reporter, $url;
    protected ?DateTimeImmutable $createdAt;
    /**
     * @var string[]
     */
    protected array $tags = [];

    protected Azdo $azdo;

    public function __construct(Azdo $azdo)
    {
        $this->azdo = $azdo;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getAssignee(): ?string
    {
        return $this->assignee;
    }

    public function getReporter(): ?string
    {
        return $this->reporter;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @throws Exception
     */
    public function loadBaseWorkItemJson(stdClass $baseWorkItemJson): void
    {
        $this->id = (int) $baseWorkItemJson->id;
        $this->priority = (int) ($baseWorkItemJson->fields->{'Microsoft.VSTS.Common.Priority'} ?? 0);
        $this->state = $baseWorkItemJson->fields->{'System.State'};
        $this->assignee = $baseWorkItemJson->fields->{'System.AssignedTo'}->displayName ?? null;
        $this->reporter = $baseWorkItemJson->fields->{'System.CreatedBy'}->uniqueName;
        $this->url = $baseWorkItemJson->_links->html->href;
        $this->createdAt = (new DateTimeImmutable($baseWorkItemJson->fields->{'System.CreatedDate'}))->setTimezone(new DateTimeZone('Europe/Prague'));
        $this->tags = $this->parseTags($baseWorkItemJson->fields->{'System.Tags'} ?? '');
        $this->title = $baseWorkItemJson->fields->{'System.Title'};
    }

    /**
     * @param string $tagsString
     * @return array
     */
    protected function parseTags(string $tagsString): array
    {
        $tags = explode(';', $tagsString);

        array_walk($tags, function(&$item) {
            $item = trim($item);
        });

        return $tags;
    }
}
