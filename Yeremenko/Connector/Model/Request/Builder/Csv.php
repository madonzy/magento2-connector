<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Model\Request\Builder;

use RuntimeException;
use Yeremenko\Connector\Api\CsvInterface;
use Yeremenko\Connector\Api\QueryInterface;
use Yeremenko\Connector\Exception\ConfigurationException;
use Yeremenko\Connector\Exception\ContractDisabledException;
use Yeremenko\Connector\Exception\SetupException;
use Yeremenko\Connector\Model\Request\RequestInterface;
use Yeremenko\Connector\Model\Connection\Csv as CsvConnection;

class Csv extends AbstractBuilder
{
    public function validate(): void
    {
        /** @var CsvInterface $contract */
        $contract = $this->getContract();

        $params = $this->getParams();

        if (!$contract->isEnabled()) {
            throw new ContractDisabledException(sprintf('Requested contract %1 is disabled.', get_class($contract)));
        }

        if (!isset($params['path'])) {
            throw new ConfigurationException('Path is required for CSV connection.');
        }

        if (!file_exists($params['path'])) {
            throw new RuntimeException("File {$params['path']} not found.");
        }
    }

    public function getRequest(): RequestInterface
    {
        /** @var CsvInterface $contract */
        $contract = $this->getContract();

        $converter = $contract->getConverter();
        $params = $this->getParams();

        // set transport data
        $this->request->setData(CsvConnection::PARAM_IS_RETURNABLE, $contract instanceof QueryInterface);
        $this->request->setData(CsvConnection::PARAM_VALIDATE_CALLBACK, [$contract, 'validate']);
        $this->request->setData(CsvConnection::PARAM_CONVERTER, $converter);
        $this->request->setData(CsvConnection::PARAM_PATH, $params['path']);
        $this->request->setData(CsvConnection::PARAM_DELIMITER, $contract->getDelimiter());
        $this->request->setData(CsvConnection::PARAM_BATCH_SIZE, $params['batch'] ?? -1);
        $this->request->setData(CsvConnection::PARAM_IS_COMPOSITE_CALLBACK, [$contract, 'isComposite']);

        return $this->request;
    }
}
