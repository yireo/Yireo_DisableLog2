<?php

declare(strict_types=1);

namespace Yireo\DisableLog2\Test\Unit\Plugin\Customer;

use Magento\Customer\Model\Logger as CustomerLogger;
use PHPUnit\Framework\TestCase;
use Yireo\DisableLog2\Plugin\Customer\Logger as CustomerLoggerPlugin;

final class LoggerTest extends TestCase
{
    public function testAroundLogDoesNotCallProceedAndReturnsSubject(): void
    {
        $customerLogger = $this->createMock(CustomerLogger::class);
        $proceed = function (): void {
            $this->fail('The original log() should not be called');
        };

        $result = (new CustomerLoggerPlugin())->aroundLog($customerLogger, $proceed, 42, ['last_login_at' => 'now']);
        $this->assertSame($customerLogger, $result);
    }
}
