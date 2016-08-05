<?php
/**
 * DisableLog2 plugin for Magento
 *
 * @package     Yireo_DisableLog2
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2016 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

namespace Yireo\DisableLog2\Plugin\PageCache;

/**
 * Class DepersonalizeChecker - Plugin for Magento\PageCache\Model\DepersonalizeChecker
 */
class DepersonalizeChecker
{
    public function aroundCheckIfDepersonalize(
        \Magento\PageCache\Model\DepersonalizeChecker $pluginSubject,
        callable $proceed
    )
    {
        return true;
    }
}