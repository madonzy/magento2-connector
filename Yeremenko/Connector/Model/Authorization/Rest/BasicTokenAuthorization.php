<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Model\Authorization\Rest;

use GuzzleHttp\RequestOptions;
use Yeremenko\Connector\Exception\ConfigurationException;
use Yeremenko\Connector\Model\Authorization\AuthorizationInterface;

class BasicTokenAuthorization implements AuthorizationInterface
{
    public const TOKEN = '---token---';

    protected array $credentials;

    public function authorize(array &$data, array &$config): void
    {
        $token = false;

        if (isset($this->credentials['token'])) {
            $token = $this->credentials['token'];
        }

        if (isset($data[self::TOKEN])) {
            $token = $data[self::TOKEN];

            unset($data[self::TOKEN]);
        }

        if (!$token) {
            throw new ConfigurationException('Unable to authorize the request. Invalid configuration provided.');
        }

        $config[RequestOptions::HEADERS]['Authorization'] = 'Bearer '.$token;
    }

    public function setCredentials(array $credentials): void
    {
        $this->credentials = $credentials;
    }
}
