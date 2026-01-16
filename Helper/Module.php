<?php

declare(strict_types=1);

namespace M2E\Mirakl\Helper;

class Module
{
    public const IDENTIFIER = 'M2E_Mirakl';

    private \Magento\Store\Model\StoreManagerInterface $storeManager;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    public function getDomain(): string
    {
        $store = $this->storeManager->getStore();
        $baseUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK, true);
        $parseUrl = parse_url($baseUrl);

        return $parseUrl['host'] ?? 'localhost';
    }
}
