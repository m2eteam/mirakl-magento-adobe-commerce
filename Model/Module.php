<?php

declare(strict_types=1);

namespace M2E\Mirakl\Model;

class Module
{
    private const CLOUD_BASE_URL = 'https://mirakl.m2e.cloud/';
    private const CLOUD_PATH_PATTERN = '?magento2_embedded=true&domain=%s&signature=%s';

    private const INSTALLED_FLAG_CONFIG_PATH = 'm2e/mirakl/installed';
    private const INIT_HOST_CONFIG_PATH = 'm2e/mirakl/init_host';

    private \Magento\Framework\App\Config\ScopeConfigInterface $config;
    private \Magento\Framework\App\Config\Storage\WriterInterface $configWriter;
    private CloudProcessor $cloudProcessor;
    private \Magento\Framework\App\CacheInterface $appCache;
    private \M2E\Mirakl\Helper\Module $moduleHelper;
    private \M2E\M2ECloudMagentoConnector\Model\IntegrationService $integrationService;

    public function __construct(
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \M2E\M2ECloudMagentoConnector\Model\IntegrationService $integrationService,
        \M2E\Mirakl\Model\CloudProcessor $salesChannelProcessor,
        \Magento\Framework\App\CacheInterface $appCache,
        \M2E\Mirakl\Helper\Module $moduleHelper
    ) {
        $this->config = $config;
        $this->configWriter = $configWriter;
        $this->cloudProcessor = $salesChannelProcessor;
        $this->appCache = $appCache;
        $this->moduleHelper = $moduleHelper;
        $this->integrationService = $integrationService;
    }

    public function isModuleConfigured(): bool
    {
        return $this->isSameHost() && $this->config->isSetFlag(self::INSTALLED_FLAG_CONFIG_PATH);
    }

    public function activate(): void
    {
        $integration = $this->integrationService->getIntegration();
        $integration->activate();

        $this->cloudProcessor->init();

        $this->setModuleAsConfigured();

        $this->cleanConfigCache();
    }

    public function getM2eCloudUrl(): string
    {
        return sprintf(
            $this->getM2eCloudBaseUrl() . self::CLOUD_PATH_PATTERN,
            $this->moduleHelper->getDomain(),
            $this->getSignature()
        );
    }

    public function getM2eCloudBaseUrl(): string
    {
        return self::CLOUD_BASE_URL;
    }

    public function resetActivation(): void
    {
        $this->configWriter->save(self::INSTALLED_FLAG_CONFIG_PATH, 0);
        $this->configWriter->save(self::INIT_HOST_CONFIG_PATH, '');
        $this->cleanConfigCache();
    }

    private function getSignature(): string
    {
        $integration = $this->integrationService->getIntegration();

        return hash_hmac(
            'sha256',
            $integration->getConsumerKey(),
            $integration->getConsumerSecret()
        );
    }

    private function setModuleAsConfigured(): void
    {
        $this->configWriter->save(self::INSTALLED_FLAG_CONFIG_PATH, 1);
        $this->configWriter->save(self::INIT_HOST_CONFIG_PATH, $this->moduleHelper->getDomain());
    }

    private function isSameHost(): bool
    {
        $hostDomain = $this->moduleHelper->getDomain();
        $initDomain = $this->config->getValue(self::INIT_HOST_CONFIG_PATH);

        return $hostDomain === $initDomain;
    }

    private function cleanConfigCache(): void
    {
        $this->appCache->clean([\Magento\Backend\Block\Menu::CACHE_TAGS, 'CONFIG']);
    }
}
