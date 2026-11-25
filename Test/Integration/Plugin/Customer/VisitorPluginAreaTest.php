<?php

declare(strict_types=1);

namespace Yireo\DisableLog2\Test\Integration\Plugin\Customer;

use Magento\Customer\Model\ResourceModel\Visitor as VisitorResource;
use Magento\Customer\Model\Visitor;
use Magento\Customer\Model\VisitorFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea adminhtml
 * @magentoDbIsolation enabled
 */
final class VisitorPluginAreaTest extends TestCase
{
    public function testVisitorIsSavedOutsideFrontend(): void
    {
        $objectManager = Bootstrap::getObjectManager();

        /** @var Visitor $visitor */
        $visitor = $objectManager->create(VisitorFactory::class)->create();
        $visitor->setSessionId('disablelog2-adminhtml-session');
        $visitor->setLastVisitAt('2026-01-01 00:00:00');
        $objectManager->create(VisitorResource::class)->save($visitor);

        $resource = $objectManager->get(ResourceConnection::class);
        $connection = $resource->getConnection();
        $select = $connection->select()
            ->from($resource->getTableName('customer_visitor'), ['COUNT(*)'])
            ->where('visitor_id = ?', (int)$visitor->getId());

        $this->assertSame(1, (int)$connection->fetchOne($select));
    }
}
