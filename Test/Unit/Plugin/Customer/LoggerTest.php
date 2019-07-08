<?php
declare(strict_types=1);

namespace Yireo\DisableLog2\Test\Unit\Plugin\Customer;

use Exception;
use PHPUnit\Framework\TestCase;
use Magento\Customer\Model\Logger as CustomerLogger;
use Yireo\DisableLog2\Plugin\Customer\Logger as CustomerLoggerPlugin;

/**
 * Class LoggerTest
 * @package Yireo\DisableLog2\Test\Unit\Plugin\Customer
 */
class LoggerTest extends TestCase
{
    /**
     *
     */
    public function testAroundLogDoesNotCallCallable()
    {
        $loggerPlugin = new CustomerLoggerPlugin();
        $this->assertInstanceOf(CustomerLoggerPlugin::class, $loggerPlugin);

        $customerLoggerMock = $this->getMockBuilder(CustomerLogger::class)
            ->disableOriginalConstructor()
            ->getMock();

        $callable = $this->getCallable();
        $loggerPlugin->aroundLog($customerLoggerMock, $callable);
    }

    /**
     * @return callable
     */
    private function getCallable()
    {
        return new class() {
            public function __invoke()
            {
                throw new Exception();
            }
        };
    }
}
