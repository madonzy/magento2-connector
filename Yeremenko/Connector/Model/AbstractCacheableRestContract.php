<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Model;

use Yeremenko\Connector\Api\CacheableInterface;
use Yeremenko\Connector\Exception\SetupException;

abstract class AbstractCacheableRestContract extends AbstractRestContract implements CacheableInterface
{
    private const PATH_KEY_TTL = 'ttl';

    public function getTtl(): int
    {
        if (!isset($this->paths[self::PATH_KEY_TTL])) {
            throw new SetupException('Unable to cache the contract. TTL configuration path is not specified.');
        }

        return (int)$this->configurationService->getRequiredConfigValue($this->paths[self::PATH_KEY_TTL]);
    }

    public function getSubjectKey(): string
    {
        return '';
    }
}
