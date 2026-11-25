<?php

declare(strict_types=1);

namespace Yireo\DisableLog2\Test\Unit\Plugin\Customer;

use Magento\Customer\Model\Logger as CustomerLogger;
use PHPUnit\Framework\TestCase;
use Yireo\DisableLog2\Config\Config;
use Yireo\DisableLog2\Plugin\Customer\Logger as CustomerLoggerPlugin;

final class LoggerTest extends TestCase
{
    public function testAroundLogDoesNotCallProceedAndReturnsSubject(): void
    {
        $customerLogger = $this->createMock(CustomerLogger::class);
        $proceed = function (): void {
            $this->fail('The original log() should not be called');
        };

        $result = $this->createPlugin(false)->aroundLog($customerLogger, $proceed, 42, ['last_login_at' => 'now']);
        $this->assertSame($customerLogger, $result);
    }

    public function testAroundLogCallsProceedWhenCustomerLogIsKept(): void
    {
        $customerLogger = $this->createMock(CustomerLogger::class);
        $arguments = [];
        $proceed = function (...$args) use (&$arguments, $customerLogger): CustomerLogger {
            $arguments = $args;
            return $customerLogger;
        };

        $result = $this->createPlugin(true)->aroundLog($customerLogger, $proceed, 42, ['last_login_at' => 'now']);
        $this->assertSame($customerLogger, $result);
        $this->assertSame([42, ['last_login_at' => 'now']], $arguments);
    }

    private function createPlugin(bool $keepCustomerLog): CustomerLoggerPlugin
    {
        $config = $this->createMock(Config::class);
        $config->method('keepCustomerLog')->willReturn($keepCustomerLog);

        return new CustomerLoggerPlugin($config);
    }
}
