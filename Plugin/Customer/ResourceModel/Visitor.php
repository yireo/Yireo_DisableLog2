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

namespace Yireo\DisableLog2\Plugin\Customer\ResourceModel;

use Magento\Customer\Model\ResourceModel\Visitor as VisitorResourceModel;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Visitor
 * @package Yireo\DisableLog2\Plugin\Customer\ResourceModel
 */
class Visitor
{
    /**
     * Convert the session ID to an integer between 0 and 4294967295 (#FFFFFFFF)
     *
     * @param VisitorResourceModel $subject
     * @param callable $proceed
     * @param AbstractModel $object
     *
     * @return VisitorResourceModel
     */
    public function aroundSave(
        VisitorResourceModel $subject,
        callable $proceed,
        AbstractModel $object
    ) {
        if ($object->getSessionId()) {
            $hexConverted = base_convert($object->getSessionId(), 36, 16);
            $truncatedHex = substr($hexConverted, 0, 8);
            $decConverted = base_convert($truncatedHex, 16, 10);
            $object->setId($decConverted);
        }

        return $subject;
    }
}
