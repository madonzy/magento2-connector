<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Model\Request\Builder;

use Exception;
use Magento\Framework\ObjectManagerInterface;
use Yeremenko\Connector\Exception\SetupException;
use Yeremenko\Connector\Model\ContractInterface;
use UnderflowException;

class BuilderFactory
{
    private ObjectManagerInterface $objectManager;
    private array $instances;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param array $instances
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        array $instances = []
    ) {
        $this->objectManager = $objectManager;
        $this->instances = $instances;
    }

    /**
     * Resolve valid builder for the contract
     *
     * @param ContractInterface $contract
     *
     * @return BuilderInterface
     * @throws SetupException
     */
    public function create(ContractInterface $contract): BuilderInterface
    {
        try {
            foreach ($this->instances as $type => $instance) {
                if ($contract instanceof $type) {
                    return $this->objectManager->create($instance);
                }
            }

            $contractClass = get_class($contract);

            throw new UnderflowException("Unable to resolve builder for the contract: {$contractClass}");
        } catch (Exception $exception) {
            throw new SetupException($exception->getMessage());
        }
    }
}
