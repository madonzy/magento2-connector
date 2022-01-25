<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Model\Authorization;

use Yeremenko\Connector\Exception\ConfigurationException;

interface AuthorizationInterface
{
    /**
     * @param array $data
     * @param array $config
     *
     * @return void
     * @throws ConfigurationException
     */
    public function authorize(array &$data, array &$config): void;

    /**
     * @param array $credentials
     *
     * @return void
     */
    public function setCredentials(array $credentials): void;
}
