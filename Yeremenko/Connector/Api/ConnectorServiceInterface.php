<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Api;

use Yeremenko\Connector\Exception\ConfigurationException;
use Yeremenko\Connector\Exception\ConnectionException;
use Yeremenko\Connector\Exception\ContractDisabledException;
use Yeremenko\Connector\Exception\EmptyResponseException;
use Yeremenko\Connector\Exception\MapperException;
use Yeremenko\Connector\Exception\SetupException;
use Yeremenko\Connector\Exception\ValidationException;
use Yeremenko\Connector\Model\Response\Wrapper;

interface ConnectorServiceInterface
{
    /**
     * Process returnable requests
     *
     * @param QueryInterface $contract
     * @param bool $force if true, guarantee APi call
     * @param array $params
     *
     * @return Wrapper
     * @throws ConnectionException
     * @throws ConfigurationException
     * @throws SetupException
     * @throws MapperException
     * @throws ValidationException
     * @throws ContractDisabledException
     * @throws EmptyResponseException
     */
    public function request(QueryInterface $contract, bool $force, array $params = []): Wrapper;

    /**
     * Process non-returnable requests
     *
     * @param CommandInterface $contract
     * @param array $params
     *
     * @return void
     * @throws ConnectionException
     * @throws ConfigurationException
     * @throws SetupException
     * @throws MapperException
     * @throws ValidationException
     * @throws ContractDisabledException
     */
    public function process(CommandInterface $contract, array $params = []): void;
}
