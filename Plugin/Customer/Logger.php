<?php
/**
 * DisableLog2 plugin for Magento
 *
 * @package     Yireo_DisableLog2
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2016 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types=1);

namespace Yireo\DisableLog2\Plugin\Customer;

use Magento\Customer\Model\Logger as OriginalLogger;

/**
 * Class Logger - Plugin for \Magento\Customer\Model\Logger
 */
class Logger
{
    /**
     * @param OriginalLogger $subject
     * @param callable $proceed
     * @return $this
     */
    public function aroundLog(
        OriginalLogger $subject,
        callable $proceed
    ) {
        return $this;
    }
}
