<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Model\Authorization\Rest;

use GuzzleHttp\RequestOptions;
use Yeremenko\Connector\Exception\ConfigurationException;
use Yeremenko\Connector\Model\Authorization\AuthorizationInterface;

class BasicAuthorization implements AuthorizationInterface
{
    protected array $credentials;

    public function authorize(array &$data, array &$config): void
    {
        if (!isset($this->credentials['username']) || !isset($this->credentials['password'])) {
            throw new ConfigurationException('Unable to authorize the request. Invalid configuration provided.');
        }

        $config[RequestOptions::AUTH] = [$this->credentials['username'], $this->credentials['password']];
    }

    public function setCredentials(array $credentials): void
    {
        $this->credentials = $credentials;
    }
}
