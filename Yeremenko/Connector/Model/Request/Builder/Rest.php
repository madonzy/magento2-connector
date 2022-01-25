<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Model\Request\Builder;

use GuzzleHttp\RequestOptions;
use Yeremenko\Connector\Api\QueryInterface;
use Yeremenko\Connector\Api\RestInterface;
use Yeremenko\Connector\Exception\ContractDisabledException;
use Yeremenko\Connector\Exception\SetupException;
use Yeremenko\Connector\Model\Request\RequestInterface;
use Yeremenko\Connector\Model\Connection\Rest as RestConnection;
use Yeremenko\Connector\Service\UriService;

class Rest extends AbstractBuilder
{
    protected UriService $uriService;

    /**
     * @inheritDoc
     *
     * @param UriService $uriService
     */
    public function __construct(
        RequestInterface $request,
        UriService $uriService
    ) {
        parent::__construct($request);

        $this->uriService = $uriService;
    }

    public function validate(): void
    {
        /** @var RestInterface $contract */
        $contract = $this->getContract();

        if (!$contract->isEnabled()) {
            throw new ContractDisabledException(sprintf('Requested contract %1 is disabled.', get_class($contract)));
        }
    }

    public function getRequest(): RequestInterface
    {
        /** @var RestInterface $contract */
        $contract = $this->getContract();

        $converter = $contract->getConverter();
        $data = $this->getData();

        // set transport data
        $this->request->setData(RestConnection::PARAM_IS_RETURNABLE, $contract instanceof QueryInterface);
        $this->request->setData(RestConnection::PARAM_VALIDATE_CALLBACK, [$contract, 'validate']);
        $this->request->setData(RestConnection::PARAM_DATA_KEY, $contract->getDataKey());
        $this->request->setData(RestConnection::PARAM_CONVERTER, $converter);
        $this->request->setData(RestConnection::PARAM_METHOD, $contract->getMethod());
        $this->request->setData(
            RestConnection::PARAM_URI,
            $this->uriService->getUri($contract->getEndpoint(), $this->getParams())
        );

        $debug = false;

        if ($contract->isDebug()) {
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            $debug = fopen("php://temp", "a+");

            $this->request->setData(RestConnection::PARAM_STREAM, $debug);
        }

        $options = [
            RequestOptions::HEADERS => $contract->getHeaders(),
            RequestOptions::VERSION => $contract->getVersion(),
            RequestOptions::DEBUG => $debug,
        ];

        $contract->getAuthorization()->authorize($data, $options);

        $options[$converter->getKey()] = $data ? $converter->prepare($data) : null;

        $this->request->setData(RestConnection::PARAM_REQUEST_OPTIONS, $options);

        $this->request->setData(
            RestConnection::PARAM_CONNECTION_CONFIG,
            [
                RequestOptions::CONNECT_TIMEOUT => $contract->getConnectionTimeout(),
                RequestOptions::TIMEOUT => $contract->getRequestTimeout(),
            ]
        );

        return $this->request;
    }
}
