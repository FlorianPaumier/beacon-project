<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud\Form;

use Devgeek\BeaconAdmin\Crud\CrudConfig;
use Devgeek\BeaconAdmin\Crud\Doctrine\AssociationMetadata;
use Devgeek\BeaconAdmin\Crud\Doctrine\EntityIntrospector;
use Devgeek\BeaconAdmin\Crud\Doctrine\FieldMetadata;
use Devgeek\BeaconAdmin\Crud\Field\AssociationField;
use Devgeek\BeaconAdmin\Crud\Field\BooleanField;
use Devgeek\BeaconAdmin\Crud\Field\DateField;
use Devgeek\BeaconAdmin\Crud\Field\DateTimeField;
use Devgeek\BeaconAdmin\Crud\Field\EmailField;
use Devgeek\BeaconAdmin\Crud\Field\EnumField;
use Devgeek\BeaconAdmin\Crud\Field\Field;
use Devgeek\BeaconAdmin\Crud\Field\NumberField;
use Devgeek\BeaconAdmin\Crud\Field\TextField;
use Devgeek\BeaconAdmin\Crud\Field\TimeField;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class FormBuilder
{
    protected const TYPE_MAP = [
        'string' => TextType::class,
        'text' => TextType::class,
        'integer' => NumberType::class,
        'float' => NumberType::class,
        'decimal' => NumberType::class,
        'boolean' => CheckboxType::class,
        'datetime' => DateTimeType::class,
        'datetimetz' => DateTimeType::class,
        'date' => DateType::class,
        'time' => TimeType::class,
        'email' => EmailType::class,
    ];

    public function __construct(
        protected FormFactoryInterface $formFactory,
        protected EntityIntrospector $introspector,
    ) {
    }

    /** @return FormInterface<mixed> */
    public function build(object $entity, CrudConfig $config): FormInterface
    {
        $entityClass = $entity::class;
        $metadata = $this->introspector->introspectFromDefault($entityClass);
        $fieldMetadataList = $metadata->getFields();
        $associationList = $metadata->getAssociations();

        $builder = $this->formFactory->createBuilder(FormType::class, $entity);

        $fields = $config->getFields();
        $fieldObjects = $config->getFieldObjects();

        if ($fieldObjects !== []) {
            foreach ($fieldObjects as $field) {
                $this->addFieldToBuilder($builder, $field, $fieldMetadataList, $associationList);
            }
        } elseif ($fields !== []) {
            foreach ($fields as $fieldName) {
                $fieldMeta = $this->findFieldMeta($fieldName, $fieldMetadataList);

                if ($fieldMeta !== null && $fieldMeta->getEnumClass() !== null) {
                    $choices = $this->buildEnumChoices($fieldMeta->getEnumClass());
                    $options = [
                        'label' => ucfirst(str_replace('_', ' ', $fieldName)),
                        'choices' => $choices,
                    ];
                    $builder->add($fieldName, ChoiceType::class, $options);
                    continue;
                }

                $symfonyType = $this->resolveType($fieldName, $fieldMetadataList);
                $options = $this->buildFieldOptions($fieldName, $fieldMetadataList);
                $builder->add($fieldName, $symfonyType, $options);
            }
        } else {
            foreach ($fieldMetadataList as $fieldMeta) {
                if ($fieldMeta->getEnumClass() !== null) {
                    $choices = $this->buildEnumChoices($fieldMeta->getEnumClass());
                    $options = [
                        'label' => ucfirst(str_replace('_', ' ', $fieldMeta->getName())),
                        'choices' => $choices,
                    ];
                    $builder->add($fieldMeta->getName(), ChoiceType::class, $options);
                    continue;
                }

                $symfonyType = self::TYPE_MAP[$fieldMeta->getType()] ?? TextType::class;
                $options = ['label' => ucfirst(str_replace('_', ' ', $fieldMeta->getName()))];

                if (!$fieldMeta->isNullable()) {
                    $options['required'] = true;
                }

                $builder->add($fieldMeta->getName(), $symfonyType, $options);
            }
        }

        foreach ($associationList as $association) {
            $alreadyAdded = false;
            foreach ($fieldObjects as $field) {
                if ($field->getName() === $association->getName()) {
                    $alreadyAdded = true;
                    break;
                }
            }
            if (!$alreadyAdded && !in_array($association->getName(), $fields, true)) {
                $builder->add($association->getName(), EntityType::class, [
                    'class' => $association->getTargetEntity(),
                    'label' => ucfirst(str_replace('_', ' ', $association->getName())),
                ]);
            }
        }

        return $builder->getForm();
    }

    /**
     * @param array<FieldMetadata>                                $fieldMetadataList
     * @param array<AssociationMetadata>                          $associationList
     * @param \Symfony\Component\Form\FormBuilderInterface<mixed> $builder
     */
    protected function addFieldToBuilder(
        \Symfony\Component\Form\FormBuilderInterface $builder,
        Field $field,
        array $fieldMetadataList,
        array $associationList,
    ): void {
        $options = [
            'label' => $field->getLabel(),
            'required' => $field->isRequired(),
        ];

        if ($field instanceof TextField) {
            if ($field->getMaxLength() !== null) {
                $options['attr']['maxlength'] = $field->getMaxLength();
            }
            if ($field->getPlaceholder() !== null) {
                $options['attr']['placeholder'] = $field->getPlaceholder();
            }
            $builder->add($field->getName(), $field->getFormType(), $options);
        } elseif ($field instanceof NumberField) {
            if ($field->getMin() !== null) {
                $options['attr']['min'] = $field->getMin();
            }
            if ($field->getMax() !== null) {
                $options['attr']['max'] = $field->getMax();
            }
            if ($field->getStep() !== null) {
                $options['attr']['step'] = $field->getStep();
            }
            $builder->add($field->getName(), $field->getFormType(), $options);
        } elseif ($field instanceof BooleanField) {
            $builder->add($field->getName(), $field->getFormType(), $options);
        } elseif ($field instanceof DateTimeField) {
            $options['widget'] = 'single_text';
            $options['format'] = $field->getFormat();
            $builder->add($field->getName(), $field->getFormType(), $options);
        } elseif ($field instanceof DateField) {
            $options['widget'] = 'single_text';
            $builder->add($field->getName(), $field->getFormType(), $options);
        } elseif ($field instanceof TimeField) {
            $options['widget'] = 'single_text';
            $builder->add($field->getName(), $field->getFormType(), $options);
        } elseif ($field instanceof EmailField) {
            $builder->add($field->getName(), $field->getFormType(), $options);
        } elseif ($field instanceof EnumField) {
            $choices = $field->getOptions();
            if ($field->getEnumClass() !== null && $choices === []) {
                $choices = $this->buildEnumChoices($field->getEnumClass());
            }
            $options['choices'] = $choices;
            $builder->add($field->getName(), $field->getFormType(), $options);
        } elseif ($field instanceof AssociationField) {
            $options['class'] = $field->getTargetEntity();
            $options['multiple'] = $field->getIsMultiple();
            $builder->add($field->getName(), $field->getFormType(), $options);
        } else {
            $builder->add($field->getName(), $field->getFormType(), $options);
        }
    }

    /**
     * @param array<FieldMetadata> $fieldMetadataList
     */
    protected function findFieldMeta(string $fieldName, array $fieldMetadataList): ?FieldMetadata
    {
        foreach ($fieldMetadataList as $fieldMeta) {
            if ($fieldMeta->getName() === $fieldName) {
                return $fieldMeta;
            }
        }

        return null;
    }

    /**
     * @param array<FieldMetadata> $fieldMetadataList
     */
    protected function resolveType(string $fieldName, array $fieldMetadataList): string
    {
        foreach ($fieldMetadataList as $fieldMeta) {
            if ($fieldMeta->getName() === $fieldName) {
                return self::TYPE_MAP[$fieldMeta->getType()] ?? TextType::class;
            }
        }

        return TextType::class;
    }

    /**
     * @return array<string, string>
     */
    private function buildEnumChoices(string $enumClass): array
    {
        $choices = [];
        foreach ($enumClass::cases() as $case) {
            $choices[$case->name] = $case instanceof \BackedEnum ? $case->value : $case->name;
        }

        return $choices;
    }

    /**
     * @param array<FieldMetadata> $fieldMetadataList
     *
     * @return array<string, mixed>
     */
    protected function buildFieldOptions(string $fieldName, array $fieldMetadataList): array
    {
        $options = ['label' => ucfirst(str_replace('_', ' ', $fieldName))];

        foreach ($fieldMetadataList as $fieldMeta) {
            if ($fieldMeta->getName() === $fieldName && !$fieldMeta->isNullable()) {
                $options['required'] = true;
                break;
            }
        }

        return $options;
    }
}
