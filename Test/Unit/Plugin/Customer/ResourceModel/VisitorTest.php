<?php

declare(strict_types=1);

namespace Yireo\DisableLog2\Test\Unit\Plugin\Customer\ResourceModel;

use Magento\Customer\Model\ResourceModel\Visitor as VisitorResourceModel;
use Magento\Framework\Model\AbstractModel;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yireo\DisableLog2\Plugin\Customer\ResourceModel\Visitor as VisitorPlugin;

final class VisitorTest extends TestCase
{
    public function testAroundSaveDoesNotCallProceed(): void
    {
        $subject = $this->createMock(VisitorResourceModel::class);
        $proceed = function (): void {
            $this->fail('The original save() should not be called');
        };

        $result = (new VisitorPlugin())->aroundSave($subject, $proceed, $this->createVisitor('abc123'));
        $this->assertSame($subject, $result);
    }

    public function testAroundSaveDoesNotSetIdWithoutSessionId(): void
    {
        $visitor = $this->createVisitor(null);
        $this->save($visitor);

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

    private function save(AbstractModel $visitor): void
    {
        $subject = $this->createMock(VisitorResourceModel::class);
        (new VisitorPlugin())->aroundSave($subject, fn () => $subject, $visitor);
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
