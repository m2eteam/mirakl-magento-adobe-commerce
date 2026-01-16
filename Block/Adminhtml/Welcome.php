<?php

declare(strict_types=1);

namespace M2E\Mirakl\Block\Adminhtml;

class Welcome extends \Magento\Backend\Block\Template
{
    public function getSubmitUrl(): string
    {
        return $this->getUrl('m2e_mirakl/dashboard/install');
    }
}
