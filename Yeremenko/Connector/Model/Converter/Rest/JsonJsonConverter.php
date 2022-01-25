<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Model\Converter\Rest;

use GuzzleHttp\RequestOptions;
use Magento\Framework\Serialize\Serializer\Json;

class JsonJsonConverter implements RestConverterInterface
{
    protected Json $serializer;

    /**
     * @param Json $serializer
     */
    public function __construct(
        Json $serializer
    ) {
        $this->serializer = $serializer;
    }

    public function prepare($data): array
    {
        return $data;
    }

    public function parse($body): array
    {
        return (array)$this->serializer->unserialize($body);
    }

    public function getKey(): string
    {
        return RequestOptions::JSON;
    }
}
