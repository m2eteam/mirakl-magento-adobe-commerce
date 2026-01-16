<?php

declare(strict_types=1);

namespace M2E\Mirakl\Block\Adminhtml\System;

class Version extends \Magento\Config\Block\System\Config\Form\Field
{
    private \M2E\Mirakl\Model\Extension $extension;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \M2E\Mirakl\Model\Extension $extension,
        array $data = []
    ) {
        $this->extension = $extension;

        parent::__construct($context, $data);
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $version = $this->extension->getVersion();

        return '<div class="control-value" style="margin-bottom:15px;">' . $this->_escaper->escapeHtml($version) . '</div>';
    }
}
