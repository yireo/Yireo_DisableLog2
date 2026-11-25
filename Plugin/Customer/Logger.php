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

class Logger
{
    /**
     * @param OriginalLogger $subject
     * @param callable $proceed
     * @param mixed $customerId
     * @param array $data
     * @return OriginalLogger
     */
    public function aroundLog(
        OriginalLogger $subject,
        callable $proceed,
        mixed $customerId,
        array $data
    ): OriginalLogger {
        return $subject;
    }
}
