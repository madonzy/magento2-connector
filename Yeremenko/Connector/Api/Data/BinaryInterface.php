<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Api\Data;

interface BinaryInterface
{
    /**
     * @param string $name
     *
     * @return BinaryInterface
     */
    public function setName(string $name): BinaryInterface;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $mimeType
     *
     * @return BinaryInterface
     */
    public function setMimeType(string $mimeType): BinaryInterface;

    /**
     * @return string
     */
    public function getMimeType(): string;

    /**
     * @param string $path
     *
     * @return BinaryInterface
     */
    public function setPath(string $path): BinaryInterface;

    /**
     * @return string
     */
    public function getPath(): string;
}
