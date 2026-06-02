<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Crud\Form;

use Devgeek\BeaconAdmin\Crud\CrudConfig;
use Devgeek\BeaconAdmin\Crud\Doctrine\AssociationMetadata;
use Devgeek\BeaconAdmin\Crud\Doctrine\EntityIntrospector;
use Devgeek\BeaconAdmin\Crud\Doctrine\EntityMetadata;
use Devgeek\BeaconAdmin\Crud\Doctrine\FieldMetadata;
use Devgeek\BeaconAdmin\Crud\Field\BooleanField;
use Devgeek\BeaconAdmin\Crud\Field\EmailField;
use Devgeek\BeaconAdmin\Crud\Field\NumberField;
use Devgeek\BeaconAdmin\Crud\Field\TextField;
use Devgeek\BeaconAdmin\Crud\Form\FormBuilder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

final class FormBuilderTest extends TestCase
{
    private function createFieldMetadata(string $name, string $type, bool $nullable): FieldMetadata
    {
        return FieldMetadata::make()->name($name)->type($type)->nullable($nullable);
    }

    /**
     * @param array<FieldMetadata> $fields
     * @param array<AssociationMetadata> $associations
     */
    private function createIntrospectorMock(array $fields, array $associations): EntityIntrospector
    {
        $entityMetadata = $this->createMock(EntityMetadata::class);
        $entityMetadata->expects($this->once())
            ->method('getFields')
            ->willReturn($fields);
        $entityMetadata->expects($this->once())
            ->method('getAssociations')
            ->willReturn($associations);

        $introspector = $this->createMock(EntityIntrospector::class);
        $introspector->expects($this->once())
            ->method('introspectFromDefault')
            ->willReturn($entityMetadata);

        return $introspector;
    }

    private function createFormBuilder(
        FormFactoryInterface $formFactory,
        EntityIntrospector $introspector,
    ): FormBuilder {
        return new FormBuilder($formFactory, $introspector);
    }

    #[Test]
    public function itCanBeInstantiated(): void
    {
        $formBuilder = new FormBuilder(
            $this->createMock(FormFactoryInterface::class),
            $this->createMock(EntityIntrospector::class),
        );

        $this->assertStringContainsString('FormBuilder', get_class($formBuilder));
    }

    #[Test]
    public function itBuildsFormFromFieldNames(): void
    {
        $entity = new \stdClass();
        $config = CrudConfig::make()
            ->entityClass(\stdClass::class)
            ->fields(['name', 'email']);

        $fields = [
            $this->createFieldMetadata('name', 'string', true),
            $this->createFieldMetadata('email', 'string', true),
        ];

        $introspector = $this->createIntrospectorMock($fields, []);

        $innerBuilder = $this->createMock(FormBuilderInterface::class);
        $innerBuilder->expects($this->exactly(2))
            ->method('add')
            ->willReturnCallback(function (string $name, string $type, array $options) use ($innerBuilder) {
                $this->assertContains($type, [TextType::class, TextType::class]);

                return $innerBuilder;
            });

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())
            ->method('createBuilder')
            ->willReturn($innerBuilder);

        $form = $this->createMock(FormInterface::class);
        $innerBuilder->expects($this->once())
            ->method('getForm')
            ->willReturn($form);

        $formBuilder = $this->createFormBuilder($formFactory, $introspector);
        $result = $formBuilder->build($entity, $config);

        $this->assertSame($form, $result);
    }

    #[Test]
    public function itBuildsFormFromFieldObjects(): void
    {
        $entity = new \stdClass();
        $field = TextField::make('title')->label('Title')->required()->maxLength(100);
        $config = CrudConfig::make()
            ->entityClass(\stdClass::class)
            ->field($field);

        $introspector = $this->createIntrospectorMock(
            [$this->createFieldMetadata('title', 'string', true)],
            [],
        );

        $innerBuilder = $this->createMock(FormBuilderInterface::class);
        $innerBuilder->expects($this->once())
            ->method('add')
            ->with('title', TextType::class, $this->callback(function (array $options) {
                return $options['label'] === 'Title'
                    && $options['required'] === true
                    && $options['attr']['maxlength'] === 100;
            }))
            ->willReturn($innerBuilder);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())
            ->method('createBuilder')
            ->willReturn($innerBuilder);

        $form = $this->createMock(FormInterface::class);
        $innerBuilder->expects($this->once())
            ->method('getForm')
            ->willReturn($form);

        $formBuilder = $this->createFormBuilder($formFactory, $introspector);
        $result = $formBuilder->build($entity, $config);

        $this->assertSame($form, $result);
    }

    #[Test]
    public function itAddsAssociations(): void
    {
        $entity = new \stdClass();
        $config = CrudConfig::make()
            ->entityClass(\stdClass::class)
            ->fields(['name']);

        $association = $this->createMock(AssociationMetadata::class);
        $association->expects($this->any())
            ->method('getName')
            ->willReturn('category');
        $association->expects($this->once())
            ->method('getTargetEntity')
            ->willReturn('App\Entity\Category');

        $introspector = $this->createIntrospectorMock(
            [$this->createFieldMetadata('name', 'string', true)],
            [$association],
        );

        $innerBuilder = $this->createMock(FormBuilderInterface::class);
        $matcher = $this->exactly(2);
        $innerBuilder->expects($matcher)
            ->method('add')
            ->willReturnCallback(function (string $name) use ($innerBuilder, $matcher) {
                match ($matcher->numberOfInvocations()) {
                    1 => $this->assertSame('name', $name),
                    2 => $this->assertSame('category', $name),
                    default => $this->fail('Unexpected invocation count'),
                };

                return $innerBuilder;
            });

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())
            ->method('createBuilder')
            ->willReturn($innerBuilder);

        $form = $this->createMock(FormInterface::class);
        $innerBuilder->expects($this->once())
            ->method('getForm')
            ->willReturn($form);

        $formBuilder = $this->createFormBuilder($formFactory, $introspector);
        $result = $formBuilder->build($entity, $config);

        $this->assertSame($form, $result);
    }

    #[Test]
    public function itBuildsFormWithNumberField(): void
    {
        $entity = new \stdClass();
        $field = NumberField::make('price')
            ->label('Price')
            ->required()
            ->min(0)
            ->max(1000)
            ->step(0.01);

        $config = CrudConfig::make()
            ->entityClass(\stdClass::class)
            ->field($field);

        $introspector = $this->createIntrospectorMock([], []);

        $innerBuilder = $this->createMock(FormBuilderInterface::class);
        $innerBuilder->expects($this->once())
            ->method('add')
            ->with('price', NumberType::class, $this->callback(function (array $options) {
                return $options['label'] === 'Price'
                    && $options['required'] === true
                    && $options['attr']['min'] === 0.0
                    && $options['attr']['max'] === 1000.0
                    && $options['attr']['step'] === 0.01;
            }))
            ->willReturn($innerBuilder);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())
            ->method('createBuilder')
            ->willReturn($innerBuilder);

        $form = $this->createMock(FormInterface::class);
        $innerBuilder->expects($this->once())
            ->method('getForm')
            ->willReturn($form);

        $formBuilder = $this->createFormBuilder($formFactory, $introspector);
        $result = $formBuilder->build($entity, $config);

        $this->assertSame($form, $result);
    }

    #[Test]
    public function itBuildsFormWithBooleanField(): void
    {
        $entity = new \stdClass();
        $field = BooleanField::make('is_active');
        $config = CrudConfig::make()
            ->entityClass(\stdClass::class)
            ->field($field);

        $introspector = $this->createIntrospectorMock([], []);

        $innerBuilder = $this->createMock(FormBuilderInterface::class);
        $innerBuilder->expects($this->once())
            ->method('add')
            ->with('is_active', CheckboxType::class, $this->anything())
            ->willReturn($innerBuilder);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())
            ->method('createBuilder')
            ->willReturn($innerBuilder);

        $form = $this->createMock(FormInterface::class);
        $innerBuilder->expects($this->once())
            ->method('getForm')
            ->willReturn($form);

        $formBuilder = $this->createFormBuilder($formFactory, $introspector);
        $result = $formBuilder->build($entity, $config);

        $this->assertSame($form, $result);
    }

    #[Test]
    public function itBuildsFormWithEmailField(): void
    {
        $entity = new \stdClass();
        $field = EmailField::make('email');
        $config = CrudConfig::make()
            ->entityClass(\stdClass::class)
            ->field($field);

        $introspector = $this->createIntrospectorMock([], []);

        $innerBuilder = $this->createMock(FormBuilderInterface::class);
        $innerBuilder->expects($this->once())
            ->method('add')
            ->with('email', EmailType::class, $this->anything())
            ->willReturn($innerBuilder);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())
            ->method('createBuilder')
            ->willReturn($innerBuilder);

        $form = $this->createMock(FormInterface::class);
        $innerBuilder->expects($this->once())
            ->method('getForm')
            ->willReturn($form);

        $formBuilder = $this->createFormBuilder($formFactory, $introspector);
        $result = $formBuilder->build($entity, $config);

        $this->assertSame($form, $result);
    }
}
