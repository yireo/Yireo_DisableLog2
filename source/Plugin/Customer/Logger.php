<?php
/**
 * DisableLog2 plugin for Magento
 *
 * @package     Yireo_DisableLog2
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2016 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

namespace Yireo\DisableLog2\Plugin\Customer;

/**
 * Class Logger - Plugin for \Magento\Customer\Model\Logger
 */
class Logger
{
    public function aroundLog(
        \Magento\Customer\Model\Logger $subject,
        callable $proceed
    )
    {
        return $this;
    }
}