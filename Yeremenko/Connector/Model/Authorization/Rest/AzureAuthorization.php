<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Model\Authorization\Rest;

use Yeremenko\Connector\Exception\ConfigurationException;
use Yeremenko\Connector\Model\Authorization\AuthorizationInterface;

class AzureAuthorization implements AuthorizationInterface
{
    protected array $credentials;

    public function authorize(array &$data, array &$config): void
    {
        if (!isset($this->credentials['posttoken']) || !isset($this->credentials['postform'])) {
            throw new ConfigurationException('Unable to authorize the request. Invalid configuration provided.');
        }

        $data['posttoken'] = $this->credentials['posttoken'];
        $data['postform'] = $this->credentials['postform'];
    }

    public function setCredentials(array $credentials): void
    {
        $this->credentials = $credentials;
    }
}
