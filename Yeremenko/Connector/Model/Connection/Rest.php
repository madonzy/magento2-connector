<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Model\Connection;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\ClientInterfaceFactory;
use GuzzleHttp\Exception\RequestException;
use Yeremenko\Connector\Exception\ConnectionException;
use Yeremenko\Connector\Exception\EmptyResponseException;
use Yeremenko\Connector\Exception\SetupException;
use Yeremenko\Connector\Exception\ValidationException;
use Yeremenko\Connector\Model\Converter\Rest\RestConverterInterface;
use Yeremenko\Connector\Model\Request\RequestInterface;
use Yeremenko\Connector\Model\StackPushInterface;
use Throwable;
use function GuzzleHttp\Psr7\str as guzzleToString;

class Rest implements ConnectionInterface
{
    public const PARAM_IS_RETURNABLE = 'is_returnable';
    public const PARAM_VALIDATE_CALLBACK = 'validate_callback';
    public const PARAM_DATA_KEY = 'data_key';
    public const PARAM_REQUEST_OPTIONS = 'request';
    public const PARAM_CONNECTION_CONFIG = 'config';
    public const PARAM_STREAM = 'stream';
    public const PARAM_CONVERTER = 'converter';
    public const PARAM_METHOD = 'method';
    public const PARAM_URI = 'uri';

    private static ClientInterface $connection;
    protected ClientInterfaceFactory $clientFactory;

    /**
     * @var StackPushInterface
     */
    protected StackPushInterface $stack;

    /**
     * RestConnection constructor.
     *
     * @param ClientInterfaceFactory $clientFactory
     * @param StackPushInterface $stack
     */
    public function __construct(
        ClientInterfaceFactory $clientFactory,
        StackPushInterface $stack
    ) {
        $this->clientFactory = $clientFactory;
        $this->stack = $stack;
    }

    public function request(RequestInterface $request): array
    {
        try {
            $connection = $this->getConnection($request->getData(self::PARAM_CONNECTION_CONFIG));

            $validate = $request->getData(self::PARAM_VALIDATE_CALLBACK);
            $dataKey = $request->getData(self::PARAM_DATA_KEY);
            $method = $request->getData(self::PARAM_METHOD);
            $uri = $request->getData(self::PARAM_URI);

            /** @var RestConverterInterface $converter */
            $converter = $request->getData(self::PARAM_CONVERTER);

            /** @var array $options */
            $options = $request->getData(self::PARAM_REQUEST_OPTIONS);

            $response = $connection->request($method, $uri, $options);

            $origData = $data = $converter->parse((string)$response->getBody());

            /** @throws ValidationException */
            $validate($data);

            $dataKeys = $dataKey ? explode('/', $dataKey) : [];

            foreach ($dataKeys as $key) {
                if (!isset($data[$key])) {
                    throw new SetupException('Invalid data key set.');
                }

                $data = $data[$key];
            }

            if (!$request->getData(self::PARAM_IS_RETURNABLE)) {
                if ($data) {
                    $this->stack->push($data);
                }

                // used only to provide response logging for command-typed requests
                return (array)$origData;
            }

            if (!is_array($data) || !$data) {
                throw new EmptyResponseException('Response data key must be an non-empty array.');
            }

            return $data;
        } catch (ValidationException | EmptyResponseException | SetupException $exception) {
            throw $exception;
        } catch (RequestException $exception) {
            $response = $exception->hasResponse() ? guzzleToString($exception->getResponse()) : 'no response available';

            throw new ConnectionException($response, $request->getData(self::PARAM_STREAM));
        } catch (Throwable $exception) {
            throw new ConnectionException($exception->getMessage(), $request->getData(self::PARAM_STREAM));
        }
    }

    /**
     * Return singleton connection instance
     *
     * @param array $config
     *
     * @return ClientInterface
     */
    private function getConnection(array $config): ClientInterface
    {
        if (!static::$connection instanceof ClientInterface) {
            static::$connection = $this->clientFactory->create($config);
        }

        return static::$connection;
    }
}
