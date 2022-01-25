<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Model;

use Yeremenko\Connector\Model\Response\Wrapper;

class LocalStorage
{
    private static array $data = [];

    /**
     * @param string $key
     *
     * @return Wrapper|null
     */
    public function get(string $key): ?Wrapper
    {
        if (!isset(self::$data[$key])) {
            return null;
        }

        return self::$data[$key];
    }

    /**
     * @param string $key
     * @param Wrapper $wrapper
     *
     * @return void
     */
    public function set(string $key, Wrapper $wrapper): void
    {
        self::$data[$key] = $wrapper;
    }
}
