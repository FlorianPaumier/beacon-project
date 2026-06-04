<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Crud;

use Devgeek\BeaconAdmin\Crud\CrudConfig;
use Devgeek\BeaconAdmin\Tests\Fixtures\TestApp\Entity\FilterTestCategory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CrudConfigFilterTest extends TestCase
{
    #[Test]
    public function itStartsWithEmptyFilters(): void
    {
        $config = CrudConfig::make();

        $this->assertSame([], $config->getFilters());
    }

    #[Test]
    public function itRegistersBooleanFilter(): void
    {
        $config = CrudConfig::make()->booleanFilter('isPublished');

        $this->assertSame([
            'isPublished' => ['operator' => 'eq', 'type' => 'boolean'],
        ], $config->getFilters());
    }

    #[Test]
    public function booleanFilterReturnsFluentStatic(): void
    {
        $config = CrudConfig::make();

        $this->assertSame($config, $config->booleanFilter('isPublished'));
    }

    #[Test]
    public function itRegistersChoiceFilterWithDefaultEqOperator(): void
    {
        $config = CrudConfig::make()
            ->choiceFilter('status', ['draft' => 'Draft', 'published' => 'Published']);

        $this->assertSame([
            'status' => [
                'operator' => 'eq',
                'type' => 'choice',
                'choices' => ['draft' => 'Draft', 'published' => 'Published'],
            ],
        ], $config->getFilters());
    }

    #[Test]
    public function itRegistersChoiceFilterWithCustomOperator(): void
    {
        $config = CrudConfig::make()
            ->choiceFilter('priority', ['high' => 'High'], 'in');

        $this->assertSame('in', $config->getFilters()['priority']['operator']);
    }

    #[Test]
    public function choiceFilterReturnsFluentStatic(): void
    {
        $config = CrudConfig::make();

        $this->assertSame($config, $config->choiceFilter('status', ['draft' => 'Draft']));
    }

    #[Test]
    public function itRegistersDateFilterWithDefaultBetweenOperator(): void
    {
        $config = CrudConfig::make()->dateFilter('publishedAt');

        $this->assertSame([
            'publishedAt' => ['operator' => 'between', 'type' => 'datetime'],
        ], $config->getFilters());
    }

    #[Test]
    public function itRegistersDateFilterWithCustomOperator(): void
    {
        $config = CrudConfig::make()->dateFilter('createdAt', 'gte');

        $this->assertSame('gte', $config->getFilters()['createdAt']['operator']);
        $this->assertSame('datetime', $config->getFilters()['createdAt']['type']);
    }

    #[Test]
    public function dateFilterReturnsFluentStatic(): void
    {
        $config = CrudConfig::make();

        $this->assertSame($config, $config->dateFilter('publishedAt'));
    }

    #[Test]
    public function itRegistersEntityFilter(): void
    {
        $config = CrudConfig::make()
            ->entityFilter('category', FilterTestCategory::class);

        $this->assertSame([
            'category' => [
                'operator' => 'eq',
                'type' => 'entity',
                'class' => FilterTestCategory::class,
            ],
        ], $config->getFilters());
    }

    #[Test]
    public function itRegistersEntityFilterWithLabelField(): void
    {
        $config = CrudConfig::make()
            ->entityFilter('category', FilterTestCategory::class, 'name');

        $this->assertSame('name', $config->getFilters()['category']['label']);
    }

    #[Test]
    public function entityFilterReturnsFluentStatic(): void
    {
        $config = CrudConfig::make();

        $this->assertSame($config, $config->entityFilter('category', FilterTestCategory::class));
    }

    #[Test]
    public function itChainsMultipleFilters(): void
    {
        $config = CrudConfig::make()
            ->booleanFilter('isPublished')
            ->choiceFilter('status', ['draft' => 'Draft'])
            ->dateFilter('publishedAt', 'between')
            ->entityFilter('category', FilterTestCategory::class);

        $filters = $config->getFilters();

        $this->assertCount(4, $filters);
        $this->assertArrayHasKey('isPublished', $filters);
        $this->assertArrayHasKey('status', $filters);
        $this->assertArrayHasKey('publishedAt', $filters);
        $this->assertArrayHasKey('category', $filters);
    }

    #[Test]
    public function laterFilterWinsForSameField(): void
    {
        $config = CrudConfig::make()
            ->booleanFilter('active')
            ->choiceFilter('active', ['yes' => 'Yes']);

        $this->assertSame('choice', $config->getFilters()['active']['type']);
    }
}
