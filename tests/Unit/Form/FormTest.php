<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Form;

use Devgeek\BeaconAdmin\Form\Form;
use Devgeek\BeaconAdmin\Support\Component;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class FormTest extends TestCase
{
    #[Test]
    public function itSetsSchemaFluently(): void
    {
        $component = $this->createMock(Component::class);
        $form = new Form($this->createMock(EntityManagerInterface::class));
        $form->schema([$component]);

        $this->assertCount(1, $form->getSchema());
        $this->assertSame($component, $form->getSchema()[0]);
    }

    #[Test]
    public function itReturnsEmptySchemaByDefault(): void
    {
        $form = new Form($this->createMock(EntityManagerInterface::class));

        $this->assertSame([], $form->getSchema());
    }

    #[Test]
    public function itSetsModelFluently(): void
    {
        $form = new Form($this->createMock(EntityManagerInterface::class));
        $form->model('App\Models\User');

        $this->assertSame('App\Models\User', $form->getModel());
    }

    #[Test]
    public function itReturnsNullModelByDefault(): void
    {
        $form = new Form($this->createMock(EntityManagerInterface::class));

        $this->assertNull($form->getModel());
    }

    #[Test]
    public function itEvaluatesClosureModel(): void
    {
        $form = new Form($this->createMock(EntityManagerInterface::class));
        $form->model(fn () => 'App\Models\User');

        $this->assertSame('App\Models\User', $form->getModel());
    }

    #[Test]
    public function itSetsStateFluently(): void
    {
        $form = new Form($this->createMock(EntityManagerInterface::class));
        $form->state(['name' => 'John']);

        $this->assertSame(['name' => 'John'], $form->getState());
    }

    #[Test]
    public function itReturnsEmptyStateByDefault(): void
    {
        $form = new Form($this->createMock(EntityManagerInterface::class));

        $this->assertSame([], $form->getState());
    }

    #[Test]
    public function itEvaluatesClosureValue(): void
    {
        $form = new Form($this->createMock(EntityManagerInterface::class));

        $result = $form->evaluate(fn () => 'resolved');

        $this->assertSame('resolved', $result);
    }

    #[Test]
    public function itReturnsRawValueWhenNotClosure(): void
    {
        $form = new Form($this->createMock(EntityManagerInterface::class));

        $this->assertSame('string', $form->evaluate('string'));
        $this->assertSame(42, $form->evaluate(42));
        $this->assertNull($form->evaluate(null));
    }

    #[Test]
    public function itFillsStateViaFill(): void
    {
        $form = new Form($this->createMock(EntityManagerInterface::class));
        $form->fill(['email' => 'test@example.com']);

        $this->assertSame(['email' => 'test@example.com'], $form->getState());
    }

    #[Test]
    public function itReturnsStaticFromFluentMethods(): void
    {
        $form = new Form($this->createMock(EntityManagerInterface::class));

        $this->assertSame($form, $form->schema([]));
        $this->assertSame($form, $form->model('App\Models\User'));
        $this->assertSame($form, $form->state([]));
    }
}
