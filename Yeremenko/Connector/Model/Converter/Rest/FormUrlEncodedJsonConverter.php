<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Model\Converter\Rest;

use GuzzleHttp\RequestOptions;

class FormUrlEncodedJsonConverter extends JsonJsonConverter
{
    public function getKey(): string
    {
        return RequestOptions::FORM_PARAMS;
    }
}
