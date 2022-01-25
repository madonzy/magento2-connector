<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Model;

use Exception;
use Magento\Framework\DataObject;
use Magento\Framework\DataObject\Mapper;
use Magento\Framework\ObjectManagerInterface;
use Yeremenko\Connector\Api\Data\AdapterInterface;
use Yeremenko\Connector\Exception\MapperException;

abstract class AbstractAdapter extends DataObject implements AdapterInterface
{
    protected ObjectManagerInterface $objectManager;

    /**
     * @inheritDoc
     *
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        parent::__construct($data);

        $this->objectManager = $objectManager;
    }

    public function intoArray(array $map): array
    {
        return $this->recursiveConvertIntoArray($map, $this);
    }

    public function fromArray(array $data, array $map): void
    {
        $this->recursivePopulateFromArray($data, $map, $this);
    }

    /**
     * Transforms array into data adapter recursively using mapper
     *
     * @param array $data
     * @param array $map
     * @param self $adapter
     *
     * @return self
     * @throws MapperException
     */
    private function recursivePopulateFromArray(array $data, array &$map, self $adapter): self
    {
        if (!$map) {
            throw new MapperException('Unable to map the object. Mapper is not defined.');
        }

        // resolve nested value objects
        foreach ($map as $externalKey => $internalKey) {
            // for valid map process we require have exactly expected structure
            if (!array_key_exists($externalKey, $data)) {
                var_dump($data);
                throw new MapperException(
                    sprintf('Unable to map the object. No item with key: %s', $externalKey)
                );
            }

            if (!is_array($internalKey)) {
                continue;
            }

            if (!isset($internalKey['__adapter'], $internalKey['__map'], $internalKey['__key'])) {
                continue;
            }

            $item = $data[$externalKey];

            try {
                if (!is_array($item)) {
                    throw new MapperException(
                        sprintf(
                            'Nested item must be an object or a list of objects. Scalar value provided for key: %s',
                            $externalKey
                        )
                    );
                }

                $nestedObjects = [];

                // if multiple adapter items
                if (array_values($item) === $item) {
                    foreach ($item as $subItem) {
                        /** @var self $subAdapter */
                        $subAdapter = $this->objectManager->create($internalKey['__adapter']);

                        $nestedObjects[] = $this->recursivePopulateFromArray(
                            $subItem,
                            $internalKey['__map'],
                            $subAdapter
                        );
                    }
                } else {
                    /** @var self $subAdapter */
                    $subAdapter = $this->objectManager->create($internalKey['__adapter']);

                    $nestedObjects = $this->recursivePopulateFromArray(
                        $item,
                        $internalKey['__map'],
                        $subAdapter
                    );
                }

                $adapter->setData($internalKey['__key'], $nestedObjects);

                unset($map[$externalKey]);
            } catch (Exception $exception) {
                throw new MapperException($exception->getMessage());
            }
        }

        $this->flattenNestedMap($map);

        Mapper::accumulateByMap($data, $adapter, $map);

        return $adapter;
    }

    /**
     * Serialize data adapter into array recursively using mapper
     *
     * @param array $map
     * @param self $adapter
     *
     * @return array
     * @throws MapperException
     */
    private function recursiveConvertIntoArray(array $map, self $adapter): array
    {
        if (!$map) {
            throw new MapperException('Unable to map the object. Mapper is not defined.');
        }

        foreach ($map as $internalKey => $externalKey) {
            // data must be set as NULL to provide valid array structure
            if (!array_key_exists($internalKey, $adapter->getData())) {
                $adapter->setData($internalKey);

                continue;
            }

            if (!is_array($externalKey)) {
                continue;
            }

            if (!isset($externalKey['__map'], $externalKey['__key'])) {
                continue;
            }

            $nestedObjects = $adapter->getData($internalKey);
            $isListOfObjects = true;

            if (!is_array($nestedObjects)) {
                $isListOfObjects = false;

                $nestedObjects = [$nestedObjects];
            }

            $result = [];

            foreach ($nestedObjects as $nestedObject) {
                $result[] = $this->recursiveConvertIntoArray($externalKey['__map'], $nestedObject);
            }

            if (!$isListOfObjects) {
                $result = current($result);
            }

            $adapter->setData($internalKey, $result);
        }

        $this->flattenNestedMap($map);

        return Mapper::accumulateByMap($adapter, [], $map);
    }

    /**
     * @param array $map
     *
     * @return void
     */
    private function flattenNestedMap(array &$map): void
    {
        $map = array_map(function ($value) {
            if (!is_array($value)) {
                return $value;
            }

            return $value['__key'] ?? $value;
        }, $map);
    }
}
