<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Api;

use Yeremenko\Connector\Model\ContractInterface;

interface CsvInterface extends ContractInterface
{
    /**
     * @return string
     */
    public function getDelimiter(): string;

    /**
     * Declares if currently fetched item is a part of composite from multiple raws
     *
     * @param array $item
     *
     * @return bool
     */
    public function isComposite(array $item): bool;
}
