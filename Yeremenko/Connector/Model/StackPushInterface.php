<?php declare(strict_types=1);

namespace Yeremenko\Connector\Model;

interface StackPushInterface
{
    /**
     * @param mixed $value
     */
    public function push($value);
}
