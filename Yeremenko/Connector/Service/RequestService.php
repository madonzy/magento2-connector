<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Service;

use Yeremenko\Connector\Exception\ConfigurationException;
use Yeremenko\Connector\Exception\ConnectionException;
use Yeremenko\Connector\Exception\ContractDisabledException;
use Yeremenko\Connector\Exception\EmptyResponseException;
use Yeremenko\Connector\Exception\SetupException;
use Yeremenko\Connector\Exception\ValidationException;
use Yeremenko\Connector\Model\ContractInterface;
use Yeremenko\Connector\Model\Request\Builder\Director;
use Yeremenko\Connector\Model\Connection\ConnectionPool;

class RequestService
{
    protected Director $director;
    protected ConnectionPool $connectionPool;
    protected DebugService $debugService;

    /**
     * RequestService constructor.
     *
     * @param Director $director
     * @param ConnectionPool $connectionPool
     * @param DebugService $debugService
     */
    public function __construct(
        Director $director,
        ConnectionPool $connectionPool,
        DebugService $debugService
    ) {
        $this->director = $director;
        $this->connectionPool = $connectionPool;
        $this->debugService = $debugService;
    }

    /**
     * @param ContractInterface $contract
     * @param array $params
     * @param array $data
     *
     * @return array
     * @throws ConfigurationException
     * @throws SetupException
     * @throws ContractDisabledException
     * @throws ConnectionException
     * @throws ValidationException
     * @throws EmptyResponseException
     */
    public function request(ContractInterface $contract, array $params, array $data = []): array
    {
        $response = [];
        $stream = null;

        // prepare request data to proceed the request
        $request = $this->director->build($contract, $data, $params);

        try {
            // choose the connection and proceed the request
            $response = $this->connectionPool->get($contract)->request($request);
        } catch (ConnectionException $exception) {
            $stream = $exception->getStream();

            throw $exception;
        } finally {
            $context = ['params' => $params, 'response' => $response, 'data' => $data];

            $this->debugService->logMessage($contract, 'Debug Request Data', $context);
            $this->debugService->logStream($contract, $stream);
        }

        return $response;
    }
}
