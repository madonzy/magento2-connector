<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Model\Connection;

use Yeremenko\Connector\Exception\ConnectionException;
use Yeremenko\Connector\Exception\EmptyResponseException;
use Yeremenko\Connector\Exception\SetupException;
use Yeremenko\Connector\Exception\ValidationException;
use Yeremenko\Connector\Model\Request\RequestInterface;

interface ConnectionInterface
{
    /**
     * Process the request directly to external service
     *
     * @param RequestInterface $request
     *
     * @return array
     * @throws ConnectionException
     * @throws ValidationException
     * @throws EmptyResponseException
     * @throws SetupException
     */
    public function request(RequestInterface $request): array;
}
