<?php

declare(strict_types=1);

namespace M2E\Mirakl\Controller\Adminhtml\Config;

class Reset extends \Magento\Backend\App\Action implements \Magento\Framework\App\Action\HttpPostActionInterface
{
    private \M2E\Mirakl\Model\Module $module;
    private \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory;
    private \M2E\M2ECloudMagentoConnector\Model\IntegrationService $integrationService;

    public function __construct(
        \M2E\Mirakl\Model\Module $module,
        \M2E\M2ECloudMagentoConnector\Model\IntegrationService $integrationService,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);

        $this->module = $module;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->integrationService = $integrationService;
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $this->module->resetActivation();

        if (!$this->integrationService->isIntegrationExist()) {
            try {
                $this->integrationService->integrationCreate();
            } catch (\Throwable $e) {
                return $result->setData(['success' => false, 'message' => $e->getMessage()]);
            }
        }

        return $result->setData(['success' => true, 'message' => __('Reset complete.')]);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('M2E_Mirakl::config');
    }
}
