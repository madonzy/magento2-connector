<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Service;

class ApiCallCounterService
{
    private static array $apiCalled = [];

    /**
     * @param string $key
     *
     * @return void
     */
    public function increase(string $key): void
    {
        if (!isset(self::$apiCalled[$key])) {
            self::$apiCalled[$key] = 1;
        } else {
            self::$apiCalled[$key]++;
        }
    }

    /**
     * @param string $key
     *
     * @return int
     */
    public function count(string $key): int
    {
        return self::$apiCalled[$key] ?? 0;
    }
}
