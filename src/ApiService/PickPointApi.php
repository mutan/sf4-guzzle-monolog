<?php
declare(strict_types=1);

namespace App\ApiService;

use Exception;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Psr\Log\LoggerInterface;

class PickPointApi
{
    const URL = 'https://e-solution.pickpoint.ru/apitest/';
    const LOGIN = 'apitest';
    const PASSWORD = 'apitest';
    const IKN = '9990003041';

    /** @var string */
    private $sessionId;

    /** @var GuzzleService */
    private $guzzleService;

    public function __construct(GuzzleService $guzzleService, LoggerInterface $pickpointLogger)
    {
        $guzzleService->setLogger($pickpointLogger);
        $this->guzzleService = $guzzleService;
    }

    /**
     * @param string $method
     * @param string $urlAddon
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function execute(string $method, string $urlAddon, array $params = [])
    {
        $options = [
            'timeout' => 60,
            'headers' => [
                'Content-type' => 'application/json',
            ]
        ];

        if ($method == 'POST' && $urlAddon != 'login') {
            $this->startSession();
            $params['SessionId'] = $this->sessionId;
        }

        $options['body'] = json_encode($params);

        try {
            $response = $this->guzzleService->getClient()->request($method,  self::URL . $urlAddon, $options);
        } catch (ClientException $e) {
            // 400-level errors
            throw new CourierApiException(sprintf(
                'Pickpoint API client error. Code: %s. Message: $%',
                $e->getCode(),
                $e->getMessage()
            ));
        } catch (ServerException $e) {
            // 500-level errors
            throw new TemporaryException(sprintf(
                'Pickpoint API server error. Code: %s. Message: $%',
                $e->getCode(),
                $e->getMessage()
            ));

            /*echo Psr7\str($e->getRequest());
            if ($e->hasResponse()) {
                echo Psr7\str($e->getResponse());
            }*/
        }

        $data = (string) $response->getBody();

        dump($data);

        if (!$data) {
            throw new CourierApiException('Pickpoint API empty body.');
        }

        return \GuzzleHttp\json_decode($data, true);
    }

    /**
     * @throws Exception
     */
    private function startSession(): void
    {
        if (!$this->sessionId) {
            $data = $this->execute('POST', 'login', ['Login' => self::LOGIN, 'Password' => self::PASSWORD]);
            if (!array_key_exists('SessionId', $data) || !$data['SessionId']) {
                throw new Exception($data['ErrorMessage']);
            }
            $this->sessionId = $data['SessionId'];
        }
    }

    public function getReestrNumber(): array
    {
        $result = $this->execute('POST', 'getreestrnumber', ['InvoiceNumber' => 'RP2653730']);

        return $result;
    }

    public function getCityList(): array
    {
        $result = $this->execute('GET', 'citylist');

        return $result;
    }

    public function getVersion(): array
    {
        return $this->execute('GET', 'version');
    }
}