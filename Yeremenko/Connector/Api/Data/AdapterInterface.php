<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Api\Data;

use Yeremenko\Connector\Exception\MapperException;

interface AdapterInterface
{
    /**
     * @param string $key
     * @param null $index
     *
     * @return mixed
     */
    public function getData($key = '', $index = null);

    /**
     * @param      $key
     * @param null $value
     *
     * @return self
     */
    public function setData($key, $value = null);

    /**
     * @param array $data
     *
     * @return self
     */
    public function addData(array $data);

    /**
     * Serialize data adapter into array using mapper
     *
     * @param array $map
     *
     * @return array
     * @throws MapperException
     */
    public function intoArray(array $map): array;

    /**
     * Transforms array into data adapter using mapper
     *
     * @param array $data
     * @param array $map
     *
     * @return void
     * @throws MapperException
     */
    public function fromArray(array $data, array $map): void;
}
