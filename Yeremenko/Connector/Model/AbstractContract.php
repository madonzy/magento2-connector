<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Model;

use Magento\Framework\Encryption\EncryptorInterface;
use Psr\Log\LoggerInterface;
use Yeremenko\Connector\Api\Data\AdapterInterface;
use Yeremenko\Connector\Model\Authorization\AuthorizationInterface;
use Yeremenko\Connector\Model\Converter\ConverterInterface;
use Yeremenko\Connector\Service\ConfigurationService;

abstract class AbstractContract implements ContractInterface
{
    private const PATH_KEY_DEBUG = 'debug';
    private const PATH_KEY_ENABLED = 'enabled';
    private const PATH_KEY_CREDENTIALS = 'credentials';

    protected ?AdapterInterface $adapter;
    protected LoggerInterface $logger;
    protected ConfigurationService $configurationService;
    protected array $paths;
    protected AuthorizationInterface $authorization;
    protected EncryptorInterface $encryptor;
    protected ConverterInterface $converter;

    /**
     * @param LoggerInterface $logger
     * @param ConfigurationService $configurationService
     * @param array $paths
     * @param AuthorizationInterface $authorization
     * @param EncryptorInterface $encryptor
     * @param ConverterInterface $converter
     * @param AdapterInterface|null $adapter
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
        $this->logger = $logger;
        $this->configurationService = $configurationService;
        $this->paths = $paths;
        $this->adapter = $adapter;
        $this->authorization = $authorization;
        $this->encryptor = $encryptor;
        $this->converter = $converter;
    }

    public function getAdapter(): ?AdapterInterface
    {
        return $this->adapter;
    }

    public function setAdapter(AdapterInterface $adapter): void
    {
        $this->adapter = $adapter;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    public function isDebug(): bool
    {
        if (!isset($this->paths[self::PATH_KEY_DEBUG])) {
            return false;
        }

        return (bool)$this->configurationService->getRequiredConfigValue($this->paths[self::PATH_KEY_DEBUG]);
    }

    public function isEnabled(): bool
    {
        if (!isset($this->paths[self::PATH_KEY_ENABLED])) {
            return true;
        }

        return (bool)$this->configurationService->getRequiredConfigValue($this->paths[self::PATH_KEY_ENABLED]);
    }

    // @codingStandardsIgnoreLine
    public function validate(array $response): void
    {
    }

    public function getAuthorization(): AuthorizationInterface
    {
        if (isset($this->paths[self::PATH_KEY_CREDENTIALS]) && is_array($this->paths[self::PATH_KEY_CREDENTIALS])) {
            $credentials = [];

            foreach ($this->paths[self::PATH_KEY_CREDENTIALS] as $key => $path) {
                $credentials[$key] = $this->encryptor->decrypt(
                    $this->configurationService->getRequiredConfigValue($path)
                );
            }

            $this->authorization->setCredentials($credentials);
        }

        return $this->authorization;
    }

    public function getConverter(): ConverterInterface
    {
        return $this->converter;
    }
}
