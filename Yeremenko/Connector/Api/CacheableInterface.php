<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Api;

use Yeremenko\Connector\Exception\ConfigurationException;
use Yeremenko\Connector\Exception\SetupException;
use Yeremenko\Connector\Model\ContractInterface;

interface CacheableInterface extends ContractInterface
{
    /**
     * @return int
     * @throws SetupException
     * @throws ConfigurationException
     */
    public function getTtl(): int;

    /**
     * Return global cache tag identifier
     *
     * Example: Yeremenko_customer_data
     *
     * @return string
     */
    public function getIdentifier(): string;

    /**
     * Return key from params to be used for the subject cache
     *
     * To get tag like:
     * "Yeremenko_ORDER_MODEL_CONTRACT_102030" you must return param key which value is 102030
     * prefix "Yeremenko_ORDER_MODEL_CONTRACT_" will be generated automatically
     * If param is empty then no subject tag is used for cache
     *
     * @return string
     */
    public function getSubjectKey(): string;
}
