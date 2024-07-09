<?php

declare(strict_types=1);

namespace Lamahive\Azdoc\Adapter\Model;

use Curl\Curl;
class Auth
{
    protected string $username;
    protected string $password;
    protected string $organization;
    protected string $project;

    /**
     * @param string $username
     * @param string $password
     * @param string $organization
     * @param string $project
     */
    public function __construct(string $username, string $password, string $organization, string $project)
    {
        $this->username = $username;
        $this->password = $password;
        $this->organization = $organization;
        $this->project = $project;
    }

    /**
     * @return string
     */
    public function getOrganization(): string
    {
        return $this->organization;
    }

    /**
     * @return string
     */
    public function getProject(): string
    {
        return $this->project;
    }

    /**
     * @return Curl
     */
    public function buildAuthCurl(): Curl
    {
        $curl = new Curl();
        $curl->setBasicAuthentication($this->username, $this->password);

        return $curl;
    }
}
