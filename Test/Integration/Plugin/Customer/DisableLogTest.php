<?php

declare(strict_types=1);

namespace Yireo\DisableLog2\Test\Integration\Plugin\Customer;

use Magento\Customer\Model\Logger as CustomerLogger;
use Magento\Customer\Model\ResourceModel\Visitor as VisitorResource;
use Magento\Customer\Model\Visitor;
use Magento\Customer\Model\VisitorFactory;
use Magento\Customer\Observer\LogLastLoginAtObserver;
use Magento\Customer\Observer\LogLastLogoutAtObserver;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DataObject;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea frontend
 * @magentoDbIsolation enabled
 */
final class DisableLogTest extends TestCase
{
    private const CUSTOMER_ID = 987654321;

    private ObjectManagerInterface $objectManager;

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }

    public function testCustomerLoggerDoesNotWriteToCustomerLog(): void
    {
        $customerLogger = $this->objectManager->get(CustomerLogger::class);
        $customerLogger->log(self::CUSTOMER_ID, ['last_login_at' => '2026-01-01 00:00:00']);
        $customerLogger->log(self::CUSTOMER_ID, ['last_logout_at' => '2026-01-01 01:00:00']);

        $this->assertSame(0, $this->countRows('customer_log', 'customer_id', self::CUSTOMER_ID));
    }

    public function testLoginAndLogoutObserversDoNotWriteToCustomerLog(): void
    {
        $observer = new Observer([
            'event' => new Event(['customer' => new DataObject(['id' => self::CUSTOMER_ID])]),
        ]);
        $this->objectManager->get(LogLastLoginAtObserver::class)->execute($observer);
        $this->objectManager->get(LogLastLogoutAtObserver::class)->execute($observer);

        $this->assertSame(0, $this->countRows('customer_log', 'customer_id', self::CUSTOMER_ID));
    }

    public function testVisitorSaveDoesNotWriteToCustomerVisitor(): void
    {
        $rowsBefore = $this->countRows('customer_visitor');

        /** @var Visitor $visitor */
        $visitor = $this->objectManager->get(VisitorFactory::class)->create();
        $visitor->setSessionId('disablelog2-integration-test-session');
        $visitor->setLastVisitAt('2026-01-01 00:00:00');
        $this->objectManager->get(VisitorResource::class)->save($visitor);

        $visitorId = $visitor->getId();
        $this->assertIsInt($visitorId);
        $this->assertGreaterThan(0, $visitorId);
        $this->assertSame($rowsBefore, $this->countRows('customer_visitor'));
        $this->assertSame(0, $this->countRows('customer_visitor', 'visitor_id', $visitorId));
    }

    public function testVisitorWithoutSessionIdIsSaved(): void
    {
        /** @var Visitor $visitor */
        $visitor = $this->objectManager->get(VisitorFactory::class)->create();
        $visitor->setLastVisitAt('2026-01-01 00:00:00');
        $this->objectManager->get(VisitorResource::class)->save($visitor);

        $visitorId = (int)$visitor->getId();
        $this->assertGreaterThan(0, $visitorId);
        $this->assertSame(1, $this->countRows('customer_visitor', 'visitor_id', $visitorId));
    }

    private function countRows(string $table, ?string $column = null, ?int $value = null): int
    {
        $resource = $this->objectManager->get(ResourceConnection::class);
        $connection = $resource->getConnection();
        $select = $connection->select()->from($resource->getTableName($table), ['COUNT(*)']);
        if ($column !== null) {
            $select->where($column . ' = ?', $value);
        }

        return (int)$connection->fetchOne($select);
    }
}
