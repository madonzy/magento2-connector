<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Service;

use Yeremenko\Connector\Api\CacheableInterface;
use Yeremenko\Connector\Api\CommandInterface;
use Yeremenko\Connector\Api\ConnectorServiceInterface;
use Yeremenko\Connector\Api\QueryInterface;
use Yeremenko\Connector\Exception\ApiCallIsRedundantException;
use Yeremenko\Connector\Exception\EmptyResponseException;
use Yeremenko\Connector\Model\LocalStorage;
use Yeremenko\Connector\Model\Response\Wrapper;
use Yeremenko\Connector\Model\Response\WrapperFactory;
use Yeremenko\Connector\Model\Serializer;

class ConnectorService implements ConnectorServiceInterface
{
    /**
     * This key is used in case when you want to send query request with body.
     * Of course, if you are in such situation that means that API was designed improperly,
     * so this is just a workaround to make it work
     */
    public const BODY_KEY = '---body---';

    protected Serializer $serializer;
    protected WrapperFactory $wrapperFactory;
    protected CacheService $cacheService;
    protected MetaContractService $metaContractService;
    protected ApiCallCounterService $apiCallCounterService;
    protected LocalStorage $localStorage;
    protected RequestService $requestService;

    /**
     * @param Serializer $serializer
     * @param WrapperFactory $wrapperFactory
     * @param CacheService $cacheService
     * @param MetaContractService $metaContractService
     * @param ApiCallCounterService $apiCallCounterService
     * @param LocalStorage $localStorage
     * @param RequestService $requestService
     */
    public function __construct(
        Serializer $serializer,
        WrapperFactory $wrapperFactory,
        CacheService $cacheService,
        MetaContractService $metaContractService,
        ApiCallCounterService $apiCallCounterService,
        LocalStorage $localStorage,
        RequestService $requestService
    ) {
        $this->serializer = $serializer;
        $this->wrapperFactory = $wrapperFactory;
        $this->cacheService = $cacheService;
        $this->metaContractService = $metaContractService;
        $this->apiCallCounterService = $apiCallCounterService;
        $this->localStorage = $localStorage;
        $this->requestService = $requestService;
    }

    public function request(QueryInterface $contract, bool $force, array $params = []): Wrapper
    {
        try {
            $response = [];
            $uniqueKey = $this->metaContractService->getUniqueKey($contract, $params);

            // if force reload is not required, check local storage and memory cache first (if apply)
            if (!$force && $contract instanceof CacheableInterface) {
                // before calling memory cache, check the local storage first (it's faster)
                if ($responseWrapper = $this->localStorage->get($uniqueKey)) {
                    return $responseWrapper;
                }

                $response = $this->cacheService->load($uniqueKey, $contract);

                // allow only one API call within single PHP request
                $this->checkApiCallRedundancy($response, $uniqueKey);
            }

            // if local storage and memory cache are empty then do an API call and populate memory cache (if apply)
            if (!$response) {
                try {
                    $data = [];

                    // sometimes, due to improper API design, you need to send query request with body and, maybe,
                    // cache it. So, this "if" statement is added only for that purpose.
                    if (isset($params[self::BODY_KEY])) {
                        $data = $params[self::BODY_KEY];

                        unset($params[self::BODY_KEY]);
                    }

                    $response = $this->requestService->request($contract, $params, $data);

                    // populate memory cache
                    if ($contract instanceof CacheableInterface) {
                        $this->cacheService->save($uniqueKey, $contract, $params, $response);
                    }
                } finally {
                    $this->apiCallCounterService->increase($uniqueKey);
                }
            }

            // convert response into adopted objects
            $items = $this->serializer->deserialize($contract, $response);

            $responseWrapper = $this->wrapperFactory->create();

            $responseWrapper->setItems($items);
            $responseWrapper->setUniqueId($uniqueKey);

            // populate local storage
            if ($contract instanceof CacheableInterface) {
                $this->localStorage->set($uniqueKey, $responseWrapper);
            }

            return $responseWrapper;
        } catch (ApiCallIsRedundantException $exception) {
            throw new EmptyResponseException($exception->getMessage());
        }
    }

    public function process(CommandInterface $contract, array $params = []): void
    {
        // serialize adopted object into array
        $data = $this->serializer->serialize($contract);

        $this->requestService->request($contract, $params, $data);
    }

    /**
     * @param array $cachedData
     * @param string $cacheKey
     *
     * @return void
     * @throws ApiCallIsRedundantException
     */
    private function checkApiCallRedundancy(array $cachedData, string $cacheKey): void
    {
        if ($cachedData) {
            return;
        }

        if ($this->apiCallCounterService->count($cacheKey)) {
            throw new ApiCallIsRedundantException('Cache is empty, but API was already called, probably with an issue.');
        }
    }
}
