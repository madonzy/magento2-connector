<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Model\Authorization\Rest;

use Yeremenko\Connector\Exception\ConfigurationException;
use Yeremenko\Connector\Model\Authorization\AuthorizationInterface;

class PowerBIAuthorization implements AuthorizationInterface
{
    private const GRANT_TYPE = 'client_credentials';

    protected array $credentials;

    public function authorize(array &$data, array &$config): void
    {
        if (!isset($this->credentials['client_id']) || !isset($this->credentials['client_secret'])) {
            throw new ConfigurationException('Unable to authorize the request. Invalid configuration provided.');
        }

        $data['client_id'] = $this->credentials['client_id'];
        $data['client_secret'] = $this->credentials['client_secret'];
        $data['grant_type'] = self::GRANT_TYPE;
    }

    public function setCredentials(array $credentials): void
    {
        $this->credentials = $credentials;
    }
}
