<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Model\Response;

use Magento\Framework\Api\Search\SearchResult;
use Yeremenko\Connector\Api\Data\AdapterInterface;
use UnderflowException;

class Wrapper extends SearchResult
{
    private const UNIQUE_ID_KEY = 'unique_id';

    /**
     * @return AdapterInterface
     * @throws UnderflowException
     */
    public function getFirstItem(): AdapterInterface
    {
        $items = $this->getItems();

        if (!$items) {
            throw new UnderflowException('Unable to fetch first item as wrapper is empty.');
        }

        $firstKey = array_key_first($items);

        return $items[$firstKey];
    }

    /**
     * @param string $id
     *
     * @return void
     */
    public function setUniqueId(string $id): void
    {
        $this->setData(self::UNIQUE_ID_KEY, $id);
    }

    /**
     * @return string|null
     */
    public function getUniqueId(): ?string
    {
        return $this->_get(self::UNIQUE_ID_KEY);
    }

    public function getItems()
    {
        $items = parent::getItems();

        return $items ? : [];
    }

    public function setTotalCount($totalCount)
    {
        return parent::setTotalCount((int)$totalCount);
    }

    public function getTotalCount()
    {
        return (int)(parent::getTotalCount() ? : count($this->getItems()));
    }
}
