<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Exception;

use RuntimeException;
use Throwable;

class ConnectionException extends RuntimeException
{
    /**
     * @var resource|null
     */
    protected $stream;

    /**
     * @inheritDoc
     *
     * @param resource|null $stream
     */
    public function __construct($message = "", $stream = null, $code = 0, Throwable $previous = null)
    {
        $this->stream = $stream;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return resource|null
     */
    public function getStream()
    {
        return $this->stream;
    }
}
