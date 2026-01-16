<?php

declare(strict_types=1);

namespace M2E\Mirakl\Controller\Adminhtml\Dashboard;

class Welcome extends AbstractController implements \Magento\Framework\App\Action\HttpGetActionInterface
{
    private \Magento\Framework\View\Result\PageFactory $resultPageFactory;
    private \M2E\Mirakl\Model\Module $module;
    private \M2E\M2ECloudMagentoConnector\Model\IntegrationService $integrationService;

    public function __construct(
        \M2E\M2ECloudMagentoConnector\Model\IntegrationService $integrationService,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\App\Action\Context $context,
        \M2E\Mirakl\Model\Module $module
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->module = $module;
        $this->integrationService = $integrationService;

        parent::__construct($context);
    }

    public function execute()
    {
        if ($this->module->isModuleConfigured()) {
            $result = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
            $result->setPath('m2e_mirakl/dashboard/index');
        } else {
            $this->initM2eIntegration();

            $result = $this->resultPageFactory->create();
            $result->setActiveMenu(self::MENU_ID);
            $result->getConfig()
                ->getTitle()
                ->set(__('Welcome to M2E Mirakl Connect!'));
        }

        return $result;
    }

    private function initM2eIntegration(): void
    {
        if (!$this->integrationService->isIntegrationExist()) {
            $integration = $this->integrationService->integrationCreate();
        } else {
            $integration = $this->integrationService->getIntegration();
        }

        $integration->prepareForInstallation();
    }
}
