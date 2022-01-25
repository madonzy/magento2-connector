<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Service;

use Yeremenko\Connector\Api\CacheableInterface;
use Yeremenko\Connector\Model\ContractInterface;

class MetaContractService
{
    /**
     * @param ContractInterface $contract
     * @param array $params
     *
     * @return string
     */
    public function getUniqueKey(ContractInterface $contract, array $params): string
    {
        $params = array_filter($params, 'is_scalar');

        ksort($params, SORT_NATURAL);

        $contractKey = str_replace('\\', '_', get_class($contract));
        $params = implode('_', array_values($params));

        return "{$contractKey}_{$params}";
    }

    /**
     * @param CacheableInterface $contract
     * @param array $params
     *
     * @return array
     */
    public function getUniqueTags(CacheableInterface $contract, array $params): array
    {
        // namespace without class name
        $entityTag = str_replace('\\', '_', (substr(get_class($contract), 0, strrpos(get_class($contract), '\\'))));

        $tags = [
            // global tag
            $contract->getIdentifier(),
            // entity tag
            $entityTag,
            // contract tag
            str_replace('\\', '_', get_class($contract)),
            // subject tag
            $this->getSubjectTag($contract, $params),
        ];

        if ($contract->getSubjectKey() && isset($params[$contract->getSubjectKey()])) {
            // subject tag
            $tags[] = "{$entityTag}_{$params[$contract->getSubjectKey()]}";
        }

        return array_values(array_filter(array_map('strtoupper', $tags)));
    }

    /**
     * Retrieve 'general' TAG for contract specified by Subject Key Value (ex. customer number)
     * Note: contract must implement getSubjectKey and return of this value must correspond to $params as key
     *
     * @param CacheableInterface $contract
     * @param array $params
     *
     * @return string
     */
    public function getSubjectTag(CacheableInterface $contract, array $params): string
    {
        $tag = '';

        if ($contract->getSubjectKey() && isset($params[$contract->getSubjectKey()])) {
            $contractKey = str_replace('\\', '_', get_class($contract)); // namespace with class name
            $subjectValue = $params[$contract->getSubjectKey()];

            $tag = "{$contractKey}_{$subjectValue}";
        }

        return strtoupper($tag);
    }
}
