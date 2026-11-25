<?php

declare(strict_types=1);

namespace Yireo\DisableLog2\Test\Unit\Plugin\Customer\ResourceModel;

use Magento\Customer\Model\ResourceModel\Visitor as VisitorResourceModel;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\Model\AbstractModel;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yireo\DisableLog2\Plugin\Customer\ResourceModel\Visitor as VisitorPlugin;

final class VisitorTest extends TestCase
{
    private const SECRET = 'first-secret';

    public function testAroundSaveDoesNotCallProceed(): void
    {
        $subject = $this->createMock(VisitorResourceModel::class);
        $proceed = function (): void {
            $this->fail('The original save() should not be called');
        };

        $result = $this->createPlugin()->aroundSave($subject, $proceed, $this->createVisitor('abc123'));
        $this->assertSame($subject, $result);
    }

    public function testAroundSaveCallsProceedWithoutSessionId(): void
    {
        $subject = $this->createMock(VisitorResourceModel::class);
        $visitor = $this->createVisitor(null);
        $proceededWith = null;
        $proceed = function (AbstractModel $object) use (&$proceededWith, $subject): VisitorResourceModel {
            $proceededWith = $object;
            return $subject;
        };

        $result = $this->createPlugin()->aroundSave($subject, $proceed, $visitor);
        $this->assertSame($subject, $result);
        $this->assertSame($visitor, $proceededWith);
        $this->assertNull($visitor->getId());
    }

    #[DataProvider('getSessionIds')]
    public function testAroundSaveSetsValidVisitorId(string $sessionId): void
    {
        $visitor = $this->createVisitor($sessionId);
        $this->save($visitor);

        $visitorId = $visitor->getId();
        $this->assertIsInt($visitorId);
        $this->assertGreaterThanOrEqual(1, $visitorId);
        $this->assertLessThanOrEqual(4294967295, $visitorId);
    }

    public static function getSessionIds(): array
    {
        return [
            ['abc123'],
            ['0'],
            [str_repeat('z', 128)],
            ['a-b,c-d,e-f'],
            ['9f8e7d6c5b4a3f2e1d0c9b8a7f6e5d4c'],
        ];
    }

    public function testAroundSaveReturnsSameIdForSameSessionId(): void
    {
        $firstVisitor = $this->createVisitor('same-session-id');
        $secondVisitor = $this->createVisitor('same-session-id');
        $this->save($firstVisitor);
        $this->save($secondVisitor);

        $this->assertSame($firstVisitor->getId(), $secondVisitor->getId());
    }

    public function testAroundSaveReturnsDifferentIdsForSessionIdsWithSamePrefix(): void
    {
        $firstVisitor = $this->createVisitor('abcdefgh0000000000000000000001');
        $secondVisitor = $this->createVisitor('abcdefgh0000000000000000000002');
        $this->save($firstVisitor);
        $this->save($secondVisitor);

        $this->assertNotSame($firstVisitor->getId(), $secondVisitor->getId());
    }

    public function testAroundSaveUsesSecretFromDeploymentConfig(): void
    {
        $sessionId = 'same-session-id';
        $firstVisitor = $this->createVisitor($sessionId);
        $secondVisitor = $this->createVisitor($sessionId);
        $this->save($firstVisitor, self::SECRET);
        $this->save($secondVisitor, 'second-secret');

        $this->assertNotSame($firstVisitor->getId(), $secondVisitor->getId());
        $expectedId = max((int)hexdec(substr(hash_hmac('sha256', $sessionId, self::SECRET), 0, 8)), 1);
        $this->assertSame($expectedId, $firstVisitor->getId());
    }

    public function testAroundSaveDoesNotUseUnkeyedHash(): void
    {
        $sessionId = 'same-session-id';
        $visitor = $this->createVisitor($sessionId);
        $this->save($visitor);

        $unkeyedId = max((int)hexdec(substr(hash('sha256', $sessionId), 0, 8)), 1);
        $this->assertNotSame($unkeyedId, $visitor->getId());
    }

    private function save(AbstractModel $visitor, string $secret = self::SECRET): void
    {
        $subject = $this->createMock(VisitorResourceModel::class);
        $this->createPlugin($secret)->aroundSave($subject, fn () => $subject, $visitor);
    }

    private function createPlugin(string $secret = self::SECRET): VisitorPlugin
    {
        $deploymentConfig = $this->createMock(DeploymentConfig::class);
        $deploymentConfig->method('get')->with('crypt/key')->willReturn($secret);

        return new VisitorPlugin($deploymentConfig);
    }

    private function createVisitor(?string $sessionId): AbstractModel
    {
        $visitor = new class extends AbstractModel {
            public function __construct()
            {
            }
        };

        if ($sessionId !== null) {
            $visitor->setData('session_id', $sessionId);
        }

        return $visitor;
    }
}
