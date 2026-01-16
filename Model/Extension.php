<?php

declare(strict_types=1);

namespace M2E\Mirakl\Model;

class Extension implements \M2E\M2ECloudMagentoConnector\Model\ExtensionInterface
{
    private Module $module;
    private \Magento\Framework\Module\ModuleListInterface $moduleList;

    public function __construct(
        \M2E\Mirakl\Model\Module $module,
        \Magento\Framework\Module\ModuleListInterface $moduleList
    ) {
        $this->module = $module;
        $this->moduleList = $moduleList;
    }

    public function getName(): string
    {
        return \M2E\Mirakl\Helper\Module::IDENTIFIER;
    }

    public function getVersion(): string
    {
        $module = $this->moduleList->getOne(\M2E\Mirakl\Helper\Module::IDENTIFIER);

        return $module ? $module['setup_version'] : '';
    }

    public function isInitCompleted(): bool
    {
        return $this->module->isModuleConfigured();
    }
}
