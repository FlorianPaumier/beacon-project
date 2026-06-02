<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Crud\Field;

use Devgeek\BeaconAdmin\Crud\Field\EmailField;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

final class EmailFieldTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $field = EmailField::make('email');

        $this->assertSame('email', $field->getName());
    }

    #[Test]
    public function itAutoGeneratesLabel(): void
    {
        $field = EmailField::make('email_address');

        $this->assertSame('Email address', $field->getLabel());
    }

    #[Test]
    public function itReturnsEmailType(): void
    {
        $field = EmailField::make('email');

        $this->assertSame(EmailType::class, $field->getFormType());
    }

    #[Test]
    public function itInheritsRequiredFromField(): void
    {
        $field = EmailField::make('email')->required();

        $this->assertTrue($field->isRequired());
    }

    #[Test]
    public function itChainsInheritedSetters(): void
    {
        $field = EmailField::make('email')
            ->label('E-mail')
            ->required(true);

        $this->assertSame('email', $field->getName());
        $this->assertSame('E-mail', $field->getLabel());
        $this->assertTrue($field->isRequired());
    }
}
