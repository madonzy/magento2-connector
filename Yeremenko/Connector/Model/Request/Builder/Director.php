<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Model\Request\Builder;

use Yeremenko\Connector\Exception\ConfigurationException;
use Yeremenko\Connector\Exception\ContractDisabledException;
use Yeremenko\Connector\Exception\SetupException;
use Yeremenko\Connector\Model\ContractInterface;
use Yeremenko\Connector\Model\Request\RequestInterface;

class Director
{
    protected BuilderFactory $builderFactory;

    /**
     * @param BuilderFactory $builderFactory
     */
    public function __construct(BuilderFactory $builderFactory)
    {
        $this->builderFactory = $builderFactory;
    }

    /**
     * @param ContractInterface $contract
     * @param array $data
     * @param array $params
     *
     * @return RequestInterface
     * @throws ConfigurationException
     * @throws SetupException
     * @throws ContractDisabledException
     */
    public function build(ContractInterface $contract, array $data, array $params): RequestInterface
    {
        $builder = $this->builderFactory->create($contract);

        $builder->setContract($contract);
        $builder->setParams($params);
        $builder->setData($data);

        $builder->validate();

        return $builder->getRequest();
    }
}
