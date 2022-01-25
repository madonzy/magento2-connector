<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Model\Converter\Csv;

use Yeremenko\Connector\Model\Converter\ConverterInterface;

class ArrayArrayConverter implements ConverterInterface
{
    private array $columns = [];

    // @codingStandardsIgnoreLine
    public function prepare($data): void
    {
    }

    public function parse($body): array
    {
        if (!is_array($body)) {
            return [];
        }

        if (!$this->columns) {
            $this->columns = array_shift($body);
        }

        return array_map(fn($item) => array_combine($this->columns, $item), $body);
    }
}
