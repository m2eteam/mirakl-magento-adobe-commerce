<?php

declare(strict_types=1);

namespace M2E\Mirakl\Block\Adminhtml;

class Dashboard extends \Magento\Backend\Block\Template
{
    private \M2E\Mirakl\Model\Module $module;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \M2E\Mirakl\Model\Module $module,
        array $data = []
    ) {
        $this->module = $module;

        parent::__construct($context, $data);
    }

    public function getIframeUrl(): string
    {
        return $this->module->getM2eCloudUrl();
    }
}
