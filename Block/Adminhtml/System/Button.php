<?php

declare(strict_types=1);

namespace M2E\Mirakl\Block\Adminhtml\System;

class Button extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $_template = 'M2E_Mirakl::system/config/button.phtml';
    private \M2E\Mirakl\Model\Module $module;
    private \Magento\Framework\UrlInterface $urlBuilder;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\UrlInterface $urlBuilder,
        \M2E\Mirakl\Model\Module $module,
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->module = $module;

        parent::__construct($context, $data);
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->_toHtml();
    }

    public function getAjaxUrl(): string
    {
        return $this->urlBuilder->getUrl('m2e_mirakl/config/reset');
    }

    public function getButtonHtml(): string
    {
        return $this->getLayout()
            ->createBlock(\Magento\Backend\Block\Widget\Button::class)
            ->setData([
                'id' => 'reset_install_btn',
                'label' => __('Reset'),
                'class' => 'action-primary',
            ])
            ->toHtml();
    }

    public function isAllowedReset(): bool
    {
        return $this->module->isModuleConfigured();
    }
}
