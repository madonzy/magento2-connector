<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Api;

use Yeremenko\Connector\Model\ContractInterface;

/**
 * If contract implements this interface that mean that contract has return data
 */
interface QueryInterface extends ContractInterface
{
}
