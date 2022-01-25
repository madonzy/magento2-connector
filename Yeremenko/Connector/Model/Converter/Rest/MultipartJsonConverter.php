<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Model\Converter\Rest;

use GuzzleHttp\RequestOptions;
use Yeremenko\Connector\Api\Data\BinaryInterface;

class MultipartJsonConverter extends JsonJsonConverter
{
    public function prepare($data): array
    {
        return $this->flattenRecursively($data);
    }

    public function getKey(): string
    {
        return RequestOptions::MULTIPART;
    }

    /**
     * Used for turning an array into a PHP friendly name.
     *
     * @param array $array
     * @param string $prefix
     * @param string $suffix
     *
     * @return array
     */
    private function flattenRecursively(array $array, string $prefix = '', string $suffix = ''): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                // @codingStandardsIgnoreLine
                $result = array_merge($result, $this->flattenRecursively($value, $prefix.$key.$suffix.'[', ']'));
            } else {
                if ($value instanceof BinaryInterface) {
                    $result[] = [
                        'name' => $prefix.$key.$suffix,
                        'filename' => $value->getName(),
                        'Mime-Type' => $value->getMimeType(),
                        // phpcs:ignore Magento2.Functions.DiscouragedFunction
                        'contents' => fopen($value->getPath(), "r"),
                    ];
                } else {
                    $result[] = [
                        'name' => $prefix.$key.$suffix,
                        'contents' => $value,
                    ];
                }
            }
        }

        return $result;
    }
}
