<?php

declare(strict_types=1);

namespace M2E\Mirakl\Controller\Adminhtml\Dashboard;

class Index extends AbstractController implements \Magento\Framework\App\Action\HttpGetActionInterface
{
    private \Magento\Framework\View\Result\PageFactory $resultPageFactory;
    private \M2E\Mirakl\Model\Module $module;

    public function __construct(
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\App\Action\Context        $context,
        \M2E\Mirakl\Model\Module                    $module
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->module = $module;
    }

    public function execute()
    {
        if ($this->module->isModuleConfigured()) {
            $result = $this->resultPageFactory->create();
            $result->setActiveMenu(self::MENU_ID);
            $result->getConfig()
                ->getTitle()
                ->set(__('M2E Mirakl Connect'));
        } else {
            $result = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
            $result->setPath('m2e_mirakl/dashboard/welcome');
        }

        return $result;
    }
}
