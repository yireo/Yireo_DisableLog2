<?php
/**
 * DisableLog2 plugin for Magento
 *
 * @package     Yireo_DisableLog2
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2016 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

namespace Yireo\DisableLog2\Plugin\Customer\ResourceModel;

/**
 * Class Visitor - Plugin for \Magento\Customer\Model\ResourceModel\Visitor
 */
class Visitor
{
    public function aroundSave(
        \Magento\Customer\Model\ResourceModel\Visitor $subject,
        callable $proceed,
        \Magento\Framework\Model\AbstractModel $object
    ) {
        if ($object->getSessionId()) {
            $object->setId(substr(base_convert($object->getSessionId(), 36, 10), 0, 10));
        }
        return $subject;
    }
}
