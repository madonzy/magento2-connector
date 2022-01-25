<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Model\Request\Builder;

use Yeremenko\Connector\Model\ContractInterface;
use Yeremenko\Connector\Model\Request\RequestInterface;

abstract class AbstractBuilder implements BuilderInterface
{
    protected RequestInterface $request;
    protected ContractInterface $contract;
    protected array $params;
    protected array $data;

    /**
     * @param RequestInterface $request
     */
    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    public function setContract(ContractInterface $contract): void
    {
        $this->contract = $contract;
    }

    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function getContract(): ContractInterface
    {
        return $this->contract;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
