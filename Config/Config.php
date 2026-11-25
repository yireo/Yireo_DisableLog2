<?php

declare(strict_types=1);

namespace Yireo\DisableLog2\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
    ) {
    }

    public function keepCustomerLog(): bool
    {
        return $this->scopeConfig->isSetFlag('customer/disable_log2/keep_customer_log');
    }
}
