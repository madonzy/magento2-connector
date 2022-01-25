<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Service;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Yeremenko\Connector\Exception\ConfigurationException;

class ConfigurationService
{
    protected ScopeConfigInterface $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param string $path
     *
     * @return string
     * @throws ConfigurationException
     */
    public function getRequiredConfigValue(string $path): string
    {
        $value = $this->scopeConfig->getValue($path);

        if ($value || $value === '0') {
            return (string)$value;
        }

        throw new ConfigurationException(sprintf('Value for %s is not configured.', $path));
    }
}
