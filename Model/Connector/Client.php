<?php

declare(strict_types=1);

namespace M2E\Mirakl\Model\Connector;

use Magento\Framework\HTTP\ClientInterface;

class Client
{
    private ClientInterface $httpClient;

    public function __construct(
        \Magento\Framework\HTTP\ClientInterface $httpClient
    ) {
        $this->httpClient = $httpClient;
    }

    public function post(string $url, array $data): array
    {
        $this->httpClient->addHeader('Content-Type', 'application/json');
        $this->httpClient->post(
            $url,
            json_encode($data)
        );

        if ($this->httpClient->getStatus() !== 200) {
            throw new \Exception('External API error: ' . $this->httpClient->getBody());
        }

        return [
            'status' => $this->httpClient->getStatus(),
            'response' => $this->httpClient->getBody(),
        ];
    }
}
