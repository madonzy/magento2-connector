<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Model;

use Yeremenko\Connector\Api\CommandInterface;
use Yeremenko\Connector\Api\Data\AdapterInterface;
use Yeremenko\Connector\Api\QueryInterface;
use Yeremenko\Connector\Exception\MapperException;

class Serializer
{
    /**
     * @param CommandInterface $contract
     *
     * @return array
     * @throws MapperException
     */
    public function serialize(CommandInterface $contract): array
    {
        $adapter = $contract->getAdapter();

        if (!$adapter) {
            return [];
        }

        return $adapter->intoArray($contract->getMap());
    }

    /**
     * @param QueryInterface $contract
     * @param array $items
     *
     * @return AdapterInterface[]
     * @throws MapperException
     */
    public function deserialize(QueryInterface $contract, array $items): array
    {
        // if data is not the list of objects
        if (array_values($items) !== $items) {
            $items = [$items];
        }

        return array_map(static function ($item) use ($contract) {
            // each time create new instance of the adapter
            $adapter = clone $contract->getAdapter();

            $adapter->fromArray($item, $contract->getMap());

            return $adapter;
        }, $items);
    }
}
