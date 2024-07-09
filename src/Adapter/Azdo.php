<?php

declare(strict_types=1);

namespace Lamahive\Azdoc\Adapter;

use Lamahive\Azdoc\Adapter\Factory\AuthFactory;
use Lamahive\Azdoc\Adapter\Model\Auth;
use function str_replace;
use function array_keys;
use function var_dump;

class Azdo
{
    const WIQL_URL = 'https://dev.azure.com/{{organization}}/{{project}}/_apis/wit/wiql/{{queryId}}?api-version=7.0';
    const WORK_ITEMS_URL = 'https://dev.azure.com/{{organization}}/{{project}}/_apis/wit/workitems?{{query}}&$expand=all&api-version=7.0';
    const QUERIES_URL = 'https://dev.azure.com/{{organization}}/{{project}}/_apis/wit/queries?$filter={{filter}}&$expand=all&api-version=7.0';

    protected AuthFactory $authFactory;

    protected Auth $auth;

    protected bool $debug = false;

    public function __construct(AuthFactory $authFactory, Auth $auth = null, bool $envConfig = true)
    {
        $this->authFactory = $authFactory;

        if ($envConfig) {
            $this->auth = $this->authFactory->create(
                [
                    $_ENV['AZDOC_USERNAME'],
                    $_ENV['AZDOC_PASSWORD'],
                    $_ENV['AZDOC_ORGANIZATION'],
                    $_ENV['AZDOC_PROJECT']
                ]
            );
        } else {
            $this->auth = $auth;
        }
    }

    public function setDebug(bool $debug): void
    {
        $this->debug = $debug;
    }

    public function setAuth(Auth $auth): void
    {
        $this->auth = $auth;
    }

    public function get(string $url): string
    {
        $curl = $this->auth->buildAuthCurl();

        if ($this->debug) {
            var_dump($url);
        }

        $curl->get($url);

        return $curl->getRawResponse();
    }

    /**
     * Processes query using _apis/with/workitems
     */
    public function getWorkItems(string $query): string
    {
        return $this->getWith(
            static::WORK_ITEMS_URL,
            $this->auth->getOrganization(),
            $this->auth->getProject(),
            [
                '{{query}}' => $query
            ]
        );
    }

    /**
     * Processes query using _apis/wit/wiql
     */
    public function getWiql(string $queryId): string
    {
        return $this->getWith(
            static::WIQL_URL,
            $this->auth->getOrganization(),
            $this->auth->getProject(),
            [
                '{{queryId}}' => $queryId
            ]
        );
    }

    /**
     * Processes query of given API $url and autofill Organization and Project from the $this->>auth (Azdoc\Adapter\Model\Auth).
     */
    public function getAuto(string $url, array $replaces): string
    {
        return $this->getWith($url, $this->auth->getOrganization(), $this->auth->getProject(), $replaces);
    }

    /**
     * Processes query of given API $url, $organization and $project.
     */
    public function getWith(string $url, string $organization, string $project, array $replaces): string
    {
        $replaces['{{organization}}'] = $organization;
        $replaces['{{project}}'] = $project;

        $apiUrl = str_replace(array_keys($replaces), $replaces, $url);

        return $this->get($apiUrl);
    }
}
