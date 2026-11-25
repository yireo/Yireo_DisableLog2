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
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\Config\ConfigOptionsListConstants;
use Magento\Framework\Model\AbstractModel;

class Visitor
{
    public function __construct(
        private readonly DeploymentConfig $deploymentConfig,
    ) {
    }

    /**
     * Skip saving the visitor, but derive a visitor ID (between 1 and 4294967295) from a keyed hash of the session ID
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
        if ($sessionId === '') {
            return $proceed($object);
        }

        $object->setId($this->getVisitorIdFromSessionId($sessionId));

        return $subject;
    }

    /**
     * @param string $sessionId
     * @return int
     */
    private function getVisitorIdFromSessionId(string $sessionId): int
    {
        $hash = hash_hmac('sha256', $sessionId, $this->getSecret());
        $visitorId = (int)hexdec(substr($hash, 0, 8));

        return max($visitorId, 1);
    }

    /**
     * @return string
     */
    private function getSecret(): string
    {
        return (string)$this->deploymentConfig->get(ConfigOptionsListConstants::CONFIG_PATH_CRYPT_KEY);
    }
}
