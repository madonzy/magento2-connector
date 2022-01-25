<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Model;

use Magento\Framework\Encryption\EncryptorInterface;
use Psr\Log\LoggerInterface;
use Yeremenko\Connector\Api\Data\AdapterInterface;
use Yeremenko\Connector\Api\RestInterface;
use Yeremenko\Connector\Exception\SetupException;
use Yeremenko\Connector\Model\Authorization\AuthorizationInterface;
use Yeremenko\Connector\Model\Converter\ConverterInterface;
use Yeremenko\Connector\Service\ConfigurationService;

abstract class AbstractRestContract extends AbstractContract implements RestInterface
{
    private const PATH_KEY_ENDPOINT = 'endpoint';
    private const PATH_KEY_CONNECTION_TIMEOUT = 'connection_timeout';
    private const PATH_KEY_REQUEST_TIMEOUT = 'request_timeout';

    protected ConverterInterface $converter;

    /**
     * @inheritDoc
     *
     * @param ConverterInterface $converter
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigurationService $configurationService,
        AuthorizationInterface $authorization,
        EncryptorInterface $encryptor,
        ConverterInterface $converter,
        array $paths = [],
        AdapterInterface $adapter = null
    ) {
        parent::__construct(
            $logger,
            $configurationService,
            $authorization,
            $encryptor,
            $converter,
            $paths,
            $adapter
        );

        $this->converter = $converter;
    }

    public function getHeaders(): array
    {
        return [];
    }

    public function getEndpoint(): string
    {
        if (!isset($this->paths[self::PATH_KEY_ENDPOINT])) {
            throw new SetupException('Unable to build the contract. Endpoint configuration path is not specified.');
        }

        return $this->configurationService->getRequiredConfigValue($this->paths[self::PATH_KEY_ENDPOINT]);
    }

    public function getConnectionTimeout(): int
    {
        if (!isset($this->paths[self::PATH_KEY_CONNECTION_TIMEOUT])) {
            return 0;
        }

        return (int)$this->configurationService->getRequiredConfigValue(
            $this->paths[self::PATH_KEY_CONNECTION_TIMEOUT]
        );
    }

    public function getRequestTimeout(): int
    {
        if (!isset($this->paths[self::PATH_KEY_REQUEST_TIMEOUT])) {
            return 0;
        }

        return (int)$this->configurationService->getRequiredConfigValue($this->paths[self::PATH_KEY_REQUEST_TIMEOUT]);
    }

    public function getVersion(): string
    {
        return '1.1';
    }

    public function getDataKey(): string
    {
        return '';
    }
}
