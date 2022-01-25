<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Model;

use Yeremenko\Connector\Api\CsvInterface;

abstract class AbstractCsvContract extends AbstractContract implements CsvInterface
{
    public function getDelimiter(): string
    {
        return ',';
    }

    public function isComposite(array $item): bool
    {
        return false;
    }
}
