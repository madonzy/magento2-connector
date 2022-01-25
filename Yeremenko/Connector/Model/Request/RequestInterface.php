<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Model\Request;

interface RequestInterface
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
     */
    public function setData($key, $value = null);

    /**
     * If $key is empty, checks whether there's any data in the object
     *
     * Otherwise checks if the specified attribute is set.
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasData($key = '');
}
