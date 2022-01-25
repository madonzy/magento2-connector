<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Model\Converter\Rest;

use Yeremenko\Connector\Model\Converter\ConverterInterface;

interface RestConverterInterface extends ConverterInterface
{
    /**
     * Return key to apply in REST library
     *
     * @return string
     */
    public function getKey(): string;
}
