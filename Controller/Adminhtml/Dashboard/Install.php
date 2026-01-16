<?php

declare(strict_types=1);

namespace M2E\Mirakl\Controller\Adminhtml\Dashboard;

class Install extends AbstractController implements \Magento\Framework\App\Action\HttpPostActionInterface
{
    private \M2E\Mirakl\Model\Module $module;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \M2E\Mirakl\Model\Module             $module
    ) {
        parent::__construct($context);
        $this->module = $module;
    }

    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        try {
            $this->module->activate();
        } catch (\Throwable $e) {
            $this->messageManager->addErrorMessage(
                __('An error occurred while updating the feature: ' . $e->getMessage())
            );

            return $resultRedirect->setPath('m2e_mirakl/dashboard/welcome');
        }

        return $resultRedirect->setPath('m2e_mirakl/dashboard/index');
    }
}
