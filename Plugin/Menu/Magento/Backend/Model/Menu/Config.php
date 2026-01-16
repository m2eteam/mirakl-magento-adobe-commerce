<?php

declare(strict_types=1);

namespace M2E\Mirakl\Plugin\Menu\Magento\Backend\Model\Menu;

class Config
{
    private bool $isProcessed = false;
    private \M2E\Mirakl\Model\Module $module;

    public function __construct(\M2E\Mirakl\Model\Module $module)
    {
        $this->module = $module;
    }

    public function afterGetMenu(\Magento\Backend\Model\Menu\Config $interceptor, \Magento\Backend\Model\Menu $result)
    {
        if ($this->isProcessed) {
            return $result;
        }

        $this->isProcessed = true;

        if (!$this->module->isModuleConfigured()) {
            $miraklItem = $result->get('M2E_Mirakl::mirakl');
            if ($miraklItem) {
                $miraklItem->setAction('m2e_mirakl/dashboard/welcome/');
            }
        }

        return $result;
    }
}
