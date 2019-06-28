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
    public function aroundSave(
        VisitorResourceModel $subject,
        callable $proceed,
        AbstractModel $object
    ) {
        if ($object->getSessionId()) {
            $converted = base_convert($object->getSessionId(), 36, 10);
            $object->setId($converted);
        }

        return $subject;
    }
}
