<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Model\Request\Builder;

use Yeremenko\Connector\Exception\ConfigurationException;
use Yeremenko\Connector\Exception\ContractDisabledException;
use Yeremenko\Connector\Exception\SetupException;
use Yeremenko\Connector\Model\ContractInterface;
use Yeremenko\Connector\Model\Request\RequestInterface;

interface BuilderInterface
{
    /**
     * @param ContractInterface $contract
     */
    public function setContract(ContractInterface $contract): void;

    /**
     * @return ContractInterface
     */
    public function getContract(): ContractInterface;

    /**
     * @param array $params
     */
    public function setParams(array $params): void;

    /**
     * @return array
     */
    public function getParams(): array;

    /**
     * @param array $data
     */
    public function setData(array $data): void;

    /**
     * @return array
     */
    public function getData(): array;

    /**
     * Validates configured contract
     *
     * @throws SetupException
     * @throws ConfigurationException
     * @throws ContractDisabledException
     */
    public function validate(): void;

    /**
     * Return built request object
     *
     * @return RequestInterface
     * @throws SetupException
     * @throws ConfigurationException
     */
    public function getRequest(): RequestInterface;
}
