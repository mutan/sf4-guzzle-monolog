<?php
declare(strict_types=1);

namespace App\ApiService;

use Exception;

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

    public function __construct(GuzzleService $guzzleService)
    {
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


        $response = $this->guzzleService->getClient()->request($method,  self::URL . $urlAddon, $options);
        dump((string) $response->getBody()); die('ok');

        return [];
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
        return $this->execute('POST', 'getreestrnumber');
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