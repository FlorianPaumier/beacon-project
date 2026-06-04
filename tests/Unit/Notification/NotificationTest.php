<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Notification;

use Devgeek\BeaconAdmin\Notification\Notification;
use Devgeek\BeaconAdmin\Notification\NotificationType;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class NotificationTest extends TestCase
{
    #[Test]
    public function successFactoryProducesSuccessType(): void
    {
        $n = Notification::success('Saved');

        $this->assertSame(NotificationType::Success, $n->type);
        $this->assertSame('Saved', $n->message);
    }

    #[Test]
    public function errorFactoryProducesErrorType(): void
    {
        $n = Notification::error('Failed');

        $this->assertSame(NotificationType::Error, $n->type);
        $this->assertSame('Failed', $n->message);
    }

    #[Test]
    public function warningFactoryProducesWarningType(): void
    {
        $n = Notification::warning('Careful');

        $this->assertSame(NotificationType::Warning, $n->type);
        $this->assertSame('Careful', $n->message);
    }

    #[Test]
    public function infoFactoryProducesInfoType(): void
    {
        $n = Notification::info('FYI');

        $this->assertSame(NotificationType::Info, $n->type);
        $this->assertSame('FYI', $n->message);
    }

    #[Test]
    public function defaultDurationIsFiveSeconds(): void
    {
        $n = Notification::info('Hi');

        $this->assertSame(5000, $n->duration);
    }

    #[Test]
    public function actionFluentSetterReturnsNewInstanceWithAction(): void
    {
        $n = Notification::info('Update available')
            ->action('View', '/posts/1');

        $this->assertSame('View', $n->actionLabel);
        $this->assertSame('/posts/1', $n->actionUrl);
    }

    #[Test]
    public function actionDoesNotMutateOriginal(): void
    {
        $original = Notification::info('Hi');
        $original->action('View', '/x');

        $this->assertNull($original->actionLabel);
        $this->assertNull($original->actionUrl);
    }

    #[Test]
    public function durationFluentSetterReturnsNewInstanceWithNewDuration(): void
    {
        $n = Notification::info('Hi')->duration(2000);

        $this->assertSame(2000, $n->duration);
    }

    #[Test]
    public function durationFluentSetterDoesNotMutateOriginal(): void
    {
        $original = Notification::info('Hi');
        $original->duration(2000);

        $this->assertSame(5000, $original->duration);
    }

    #[Test]
    public function stickyReturnsZeroDuration(): void
    {
        $n = Notification::info('Hi')->sticky();

        $this->assertSame(0, $n->duration);
        $this->assertTrue($n->isSticky());
    }

    #[Test]
    public function isStickyIsTrueOnlyForZeroDuration(): void
    {
        $this->assertFalse(Notification::info('Hi')->isSticky());
        $this->assertFalse(Notification::info('Hi')->duration(1500)->isSticky());
        $this->assertTrue(Notification::info('Hi')->sticky()->isSticky());
    }

    #[Test]
    public function toArrayContainsAllFields(): void
    {
        $n = Notification::success('Saved')
            ->action('Undo', '/undo')
            ->duration(3000);

        $array = $n->toArray();

        $this->assertSame('success', $array['type']);
        $this->assertSame('Saved', $array['message']);
        $this->assertSame('Undo', $array['action_label']);
        $this->assertSame('/undo', $array['action_url']);
        $this->assertSame(3000, $array['duration']);
        $this->assertFalse($array['sticky']);
    }

    #[Test]
    public function toArrayMarksStickyAsTrueWhenDurationIsZero(): void
    {
        $n = Notification::error('Boom')->sticky();

        $array = $n->toArray();

        $this->assertTrue($array['sticky']);
        $this->assertSame(0, $array['duration']);
    }

    #[Test]
    public function toArrayHasNullActionFieldsByDefault(): void
    {
        $n = Notification::info('Hi');

        $array = $n->toArray();

        $this->assertNull($array['action_label']);
        $this->assertNull($array['action_url']);
    }

    #[Test]
    public function fluentChainPreservesAllSetters(): void
    {
        $n = Notification::warning('Careful')
            ->action('Review', '/review')
            ->duration(0);

        $this->assertSame(NotificationType::Warning, $n->type);
        $this->assertSame('Careful', $n->message);
        $this->assertSame('Review', $n->actionLabel);
        $this->assertSame('/review', $n->actionUrl);
        $this->assertSame(0, $n->duration);
    }
}
