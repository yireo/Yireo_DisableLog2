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

class Visitor
{
    /**
     * Skip saving the visitor, but derive a visitor ID (between 1 and 4294967295) from the full session ID
     *
     * @param VisitorResourceModel $subject
     * @param callable $proceed
     * @param AbstractModel $object
     * @return VisitorResourceModel
     */
    public function aroundSave(
        VisitorResourceModel $subject,
        callable $proceed,
        AbstractModel $object
    ): VisitorResourceModel {
        $sessionId = (string)$object->getSessionId();
        if ($sessionId !== '') {
            $object->setId($this->getVisitorIdFromSessionId($sessionId));
        }

        return $subject;
    }

    /**
     * @param string $sessionId
     * @return int
     */
    private function getVisitorIdFromSessionId(string $sessionId): int
    {
        $visitorId = (int)hexdec(substr(hash('sha256', $sessionId), 0, 8));

        return max($visitorId, 1);
    }
}
