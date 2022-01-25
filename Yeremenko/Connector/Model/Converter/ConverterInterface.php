<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Model\Converter;

interface ConverterInterface
{
    /**
     * @param mixed $data
     *
     * @return mixed
     */
    public function prepare($data);

    /**
     * @param mixed $body
     *
     * @return mixed
     */
    public function parse($body);
}
