<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Model;

use Psr\Log\LoggerInterface;
use Yeremenko\Connector\Api\Data\AdapterInterface;
use Yeremenko\Connector\Exception\ConfigurationException;
use Yeremenko\Connector\Exception\ValidationException;
use Yeremenko\Connector\Model\Authorization\AuthorizationInterface;
use Yeremenko\Connector\Model\Converter\ConverterInterface;

interface ContractInterface
{
    /**
     * If false, exception will be thrown
     *
     * @return bool
     * @throws ConfigurationException
     */
    public function isEnabled(): bool;

    /**
     * If true, extra trace will be provided
     *
     * @return bool
     * @throws ConfigurationException
     */
    public function isDebug(): bool;

    /**
     * Check if response is valid
     *
     * @param array $response
     *
     * @return void
     * @throws ValidationException
     */
    public function validate(array $response): void;

    /**
     * @return AdapterInterface|null
     */
    public function getAdapter(): ?AdapterInterface;

    /**
     * @param AdapterInterface $adapter
     *
     * @return void
     */
    public function setAdapter(AdapterInterface $adapter): void;

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface;

    /**
     * @return AuthorizationInterface
     */
    public function getAuthorization(): AuthorizationInterface;

    /**
     * @return ConverterInterface
     */
    public function getConverter(): ConverterInterface;

    /**
     * Return mapped array
     *
     * If export, use:
     *  [
     *     '{inner_key}' => '{external_key}'
     *  ]
     *
     * If import, use:
     *  [
     *     '{external_key}' => '{inner_key}'
     *  ]
     *
     * If import with nested value objects (must implement AdapterInterface), use:
     *  [
     *     '{external_key}' => [
     *          '__key' => '{inner_key}',
     *          '__adapter' => '{value object adapter class name}',
     *          '__map' => [
     *              '{external_key}' => '{inner_key}'
     *          ]
     *      ]
     *  ]
     *
     * If export with nested value objects (must implement AdapterInterface), use:
     *  [
     *     '{inner_key}' => [
     *          '__key' => '{external_key}',
     *          '__map' => [
     *              '{inner_key}' => '{external_key}'
     *          ]
     *      ]
     *  ]
     *
     * @return array
     */
    public function getMap(): array;
}
