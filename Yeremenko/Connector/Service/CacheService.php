<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Service;

use InvalidArgumentException;
use Magento\Framework\App\Cache\Type\FrontendPool;
use Magento\Framework\Serialize\Serializer\Json;
use Yeremenko\Connector\Api\CacheableInterface;
use Yeremenko\Connector\Exception\SetupException;
use Zend_Cache;

class CacheService
{
    protected FrontendPool $frontendPool;
    protected Json $json;
    protected MetaContractService $metaContractService;

    /**
     * CacheService constructor.
     *
     * @param FrontendPool $frontendPool
     * @param Json $json
     * @param MetaContractService $metaContractService
     */
    public function __construct(
        FrontendPool $frontendPool,
        Json $json,
        MetaContractService $metaContractService
    ) {
        $this->frontendPool = $frontendPool;
        $this->json = $json;
        $this->metaContractService = $metaContractService;
    }

    /**
     * @param string $cacheKey
     * @param CacheableInterface $contract
     *
     * @return array
     */
    public function load(string $cacheKey, CacheableInterface $contract): array
    {
        $result = [];
        $cache = $this->frontendPool->get($contract->getIdentifier());
        $data = (string)$cache->load($cacheKey);

        if ($data) {
            try {
                $data = $this->json->unserialize($data);

                if (is_array($data)) {
                    $result = $data;
                }
            } catch (InvalidArgumentException $exception) {
                return [];
            }
        }

        return $result;
    }

    /**
     * @param string $cacheKey
     * @param CacheableInterface $contract
     * @param array $params
     * @param array $data
     *
     * @return void
     * @throws SetupException
     */
    public function save(string $cacheKey, CacheableInterface $contract, array $params, array $data): void
    {
        $cache = $this->frontendPool->get($contract->getIdentifier());

        try {
            $cache->save(
                $this->json->serialize($data),
                $cacheKey,
                $this->metaContractService->getUniqueTags($contract, $params),
                $contract->getTtl()
            );
        } catch (InvalidArgumentException $exception) {
            return;
        }
    }
}
