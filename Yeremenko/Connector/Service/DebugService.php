<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Service;

use Yeremenko\Connector\Model\ContractInterface;

class DebugService
{
    /**
     * Log debug information
     *
     * @param ContractInterface $contract
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function logMessage(ContractInterface $contract, string $message, array $context): void
    {
        if (!$contract->isDebug()) {
            return;
        }

        $contract->getLogger()->debug($message, $context);
    }

    /**
     * Log debug information from stream
     *
     * @param ContractInterface $contract
     * @param resource|null $stream
     *
     * @return void
     */
    public function logStream(ContractInterface $contract, $stream): void
    {
        if (!$contract->isDebug() || !is_resource($stream)) {
            return;
        }

        // phpcs:disable Magento2.Exceptions.TryProcessSystemResources.MissingTryCatch
        // phpcs:disable Magento2.Functions.DiscouragedFunction
        rewind($stream);

        $contract->getLogger()->debug(stream_get_contents($stream));

        fclose($stream);
        // phpcs:enable Magento2.Functions.DiscouragedFunction
        // phpcs:enable Magento2.Exceptions.TryProcessSystemResources.MissingTryCatch
    }
}
