<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Api;

use Yeremenko\Connector\Model\ContractInterface;

/**
 * If contract implements this interface that mean that contract has no return data
 */
interface CommandInterface extends ContractInterface
{
}
