<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Api;

use Yeremenko\Connector\Exception\ConfigurationException;
use Yeremenko\Connector\Exception\SetupException;
use Yeremenko\Connector\Model\ContractInterface;

interface RestInterface extends ContractInterface
{
    /**
     * Return HTTP method per payload
     *
     * @return string
     */
    public function getMethod(): string;

    /**
     * Return HTTP headers
     *
     * @return string[]
     */
    public function getHeaders(): array;

    /**
     * Return HTTP version
     *
     * @return string
     */
    public function getVersion(): string;

    /**
     * Return full URI
     *
     * @return string
     * @throws SetupException
     * @throws ConfigurationException
     */
    public function getEndpoint(): string;

    /**
     * Time in seconds for connecting to server
     *
     * @return int
     * @throws ConfigurationException
     */
    public function getConnectionTimeout(): int;

    /**
     * Max time in second of request
     *
     * @return int
     * @throws ConfigurationException
     */
    public function getRequestTimeout(): int;

    /**
     * Return key for data mapper process
     *
     * If data key is nested, just provide it with "/"
     * To get all the response, just return "" (empty string)
     *
     * @return string
     */
    public function getDataKey(): string;
}
