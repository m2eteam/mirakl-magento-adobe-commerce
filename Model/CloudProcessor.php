<?php

declare(strict_types=1);

namespace M2E\Mirakl\Model;

class CloudProcessor
{
    private const CLOUD_API_URL = 'https://m2e.cloud/api/v1/magento2/account/login/';

    private \M2E\Mirakl\Model\Connector\Client $httpClient;
    private \Magento\Store\Model\StoreManagerInterface $storeManager;
    private \Magento\Backend\Helper\Data $backendHelper;
    private \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig;
    private \M2E\Mirakl\Helper\Module $moduleHelper;
    private \M2E\M2ECloudMagentoConnector\Model\IntegrationService $integrationService;

    public function __construct(
        \M2E\Mirakl\Model\Connector\Client $httpClient,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \M2E\M2ECloudMagentoConnector\Model\IntegrationService $integrationService,
        \M2E\Mirakl\Helper\Module $moduleHelper
    ) {
        $this->httpClient = $httpClient;
        $this->storeManager = $storeManager;
        $this->backendHelper = $backendHelper;
        $this->scopeConfig = $scopeConfig;
        $this->moduleHelper = $moduleHelper;
        $this->integrationService = $integrationService;
    }

    public function init(): void
    {
        $integration = $this->integrationService->getIntegration();

        $defaultStoreView = $this->storeManager->getDefaultStoreView();

        $store = $this->getDefaultStore();

        $baseUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK, true);

        $data = [
            'app_name' => 'mirakl',
            'general_info' => [
                'currency' => $store->getCurrentCurrencyCode(),
                'country_code' => $this->getStoreConfig('general/country/default'),
                'domain' => $this->moduleHelper->getDomain(),
                'timezone' => $this->getStoreConfig('general/locale/timezone'),
                'store_view_code' => $defaultStoreView->getCode(),
                'store_view_title' => $defaultStoreView->getName(),
                'weight_unit' => $this->getStoreConfig('general/locale/weight_unit')
            ],
            'user_info' => [
                'email' => $this->getStoreConfig('contact/email/recipient_email'),
                'name' => $this->getStoreConfig('general/store_information/name'),
            ],
            'urls' => [
                'frontend_url' => $baseUrl,
                'api_root_url' => $this->getRestApiUrl($baseUrl),
                'admin_url' => $this->getAdminUrl(),
                'root_media_folder_url' => $this->getProductMediaUrl($store),
            ],
            'credentials' => [
                'consumer_key' => $integration->getConsumerKey(),
                'consumer_secret' => $integration->getConsumerSecret(),
                'access_token' => $integration->getToken(),
                'access_token_secret' => $integration->getTokenSecret(),
            ],
        ];

        $this->httpClient->post(
            $this->getApiUrl(),
            $data
        );
    }

    public function getApiUrl(): string
    {
        return self::CLOUD_API_URL;
    }

    private function getStoreConfig(string $configPath): ?string
    {
        return $this->scopeConfig->getValue($configPath, \Magento\Store\Model\ScopeInterface::SCOPE_STORE) ?? null;
    }

    private function getRestApiUrl(string $baseUrl): string
    {
        return $baseUrl . 'rest/';
    }

    private function getProductMediaUrl(\Magento\Store\Model\Store $store): string
    {
        return $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA, true) . 'catalog/product';
    }

    private function getDefaultStore(): \Magento\Store\Model\Store
    {
        return $this->storeManager->getStore();
    }

    private function getAdminUrl(): string
    {
        $adminStore = $this->storeManager->getStore(\Magento\Store\Model\Store::ADMIN_CODE);

        return $adminStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK, true)
            . $this->backendHelper->getAreaFrontName()
            . '/';
    }
}
