<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Model\Connection;

use Exception;
use Magento\Framework\ObjectManagerInterface;
use Yeremenko\Connector\Exception\SetupException;
use Yeremenko\Connector\Model\ContractInterface;
use UnderflowException;

class ConnectionPool
{
    private ObjectManagerInterface $objectManager;

    /**
     * @var array
     */
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
     * Resolve valid connection for the contract
     *
     * @param ContractInterface $contract
     *
     * @return ConnectionInterface
     * @throws SetupException
     */
    public function get(ContractInterface $contract): ConnectionInterface
    {
        try {
            foreach ($this->instances as $type => $instance) {
                if ($contract instanceof $type) {
                    return $this->objectManager->get($instance);
                }
            }

            $contractClass = get_class($contract);

            throw new UnderflowException("Unable to resolve connection for the contract: {$contractClass}");
        } catch (Exception $exception) {
            throw new SetupException($exception->getMessage());
        }
    }
}
