<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Model\Connection;

use Magento\Framework\File\Csv as CsvReader;
use Yeremenko\Connector\Exception\ConnectionException;
use Yeremenko\Connector\Exception\SetupException;
use Yeremenko\Connector\Exception\ValidationException;
use Yeremenko\Connector\Model\Converter\Rest\RestConverterInterface;
use Yeremenko\Connector\Model\Request\RequestInterface;
use Throwable;

class Csv implements ConnectionInterface
{
    public const PARAM_IS_RETURNABLE = 'is_returnable';
    public const PARAM_VALIDATE_CALLBACK = 'validate_callback';
    public const PARAM_CONVERTER = 'converter';
    public const PARAM_PATH = 'path';
    public const PARAM_DELIMITER = 'delimiter';
    public const PARAM_BATCH_SIZE = 'batch_size';
    public const PARAM_IS_COMPOSITE_CALLBACK = 'is_composite_callback';

    protected CsvReader $csvReader;
    protected array $file = [];
    private array $previousRaw = [];

    /**
     * @param CsvReader $csvReader
     */
    public function __construct(
        CsvReader $csvReader
    ) {
        $this->csvReader = $csvReader;
    }

    public function request(RequestInterface $request): array
    {
        try {
            $validate = $request->getData(self::PARAM_VALIDATE_CALLBACK);
            $path = $request->getData(self::PARAM_PATH);
            $delimiter = $request->getData(self::PARAM_DELIMITER);
            $batchSize = $request->getData(self::PARAM_BATCH_SIZE);
            $isComposite = $request->getData(self::PARAM_IS_COMPOSITE_CALLBACK);

            /** @var RestConverterInterface $converter */
            $converter = $request->getData(self::PARAM_CONVERTER);

            $data = $converter->parse($this->getNextBatch($path, $delimiter, $batchSize, $isComposite));

            /** @throws ValidationException */
            $validate($data);

            if (!$request->getData(self::PARAM_IS_RETURNABLE)) {
                return [];
            }

            return $data;
        } catch (ValidationException | SetupException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            throw new ConnectionException($exception->getMessage());
        }
    }

    /**
     * @param string $path
     * @param string $delimiter
     * @param int $batchSize
     * @param callable $isComposite
     *
     * @return array
     */
    private function getNextBatch(string $path, string $delimiter, int $batchSize, callable $isComposite): array
    {
        $key = md5($path);

        if (!isset($this->file[$key]) || !is_resource($this->file[$key])) {
            $this->file[$key] = fopen($path, 'r');

            $batchSize++; // if the file just initialized, we need to omit columns raw and proceed with valid batch number
        }

        $count = 0;
        $data = [];

        if ($this->previousRaw) {
            $data[] = $this->previousRaw;

            $count = 1; // if we add extra raw, we need to reduce batch size by 1
            $this->previousRaw = [];
        }

        while ($rowData = fgetcsv($this->file[$key], 0, $delimiter)) {
            if ($count === $batchSize) {
                if ($isComposite($rowData)) {
                    $data[] = $rowData;

                    continue;
                }

                $this->previousRaw = $rowData;

                break;
            }

            $data[] = $rowData;

            // count only full composite raws
            if (!$isComposite($rowData)) {
                $count++;
            }
        }

        if (!$data) {
            fclose($this->file[$key]);

            unset($this->file[$key]);
        }

        return $data;
    }
}
