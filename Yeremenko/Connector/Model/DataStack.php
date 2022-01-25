<?php declare(strict_types=1);

namespace Yeremenko\Connector\Model;

use SplStack;
use Yeremenko\Connector\Api\StackPopInterface;

class DataStack extends SplStack implements StackPushInterface, StackPopInterface
{

}
