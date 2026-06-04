<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Fixtures\TestApp\Controller;

use Devgeek\BeaconAdmin\Controller\AbstractCrudController;
use Devgeek\BeaconAdmin\Crud\CrudConfig;
use Devgeek\BeaconAdmin\Tests\Fixtures\TestApp\Entity\FilterTestCategory;
use Devgeek\BeaconAdmin\Tests\Fixtures\TestApp\Entity\FilterTestPost;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/filter-test-posts', name: 'filter_test_post_')]
final class FilterTestPostCrudController extends AbstractCrudController
{
    protected function configureCrud(CrudConfig $config): void
    {
        $config
            ->entityLabel('Test Post')
            ->entityLabelPlural('Test Posts')
            ->fields(['title', 'isPublished', 'status', 'publishedAt', 'category'])
            ->booleanFilter('isPublished')
            ->choiceFilter('status', ['draft' => 'Draft', 'published' => 'Published'])
            ->dateFilter('publishedAt', 'between')
            ->entityFilter('category', FilterTestCategory::class)
            ->searchableFields(['title'])
            ->pageSize(25);
    }

    protected function getEntityClass(): string
    {
        return FilterTestPost::class;
    }

    #[Route('/{id}', name: 'list_show', methods: ['GET'])]
    public function show(Request $request, string $id): Response
    {
        return parent::show($request, $id);
    }

    #[Route('/{id}/edit', name: 'list_edit', methods: ['GET', 'POST'])]
    public function update(Request $request, string $id): Response
    {
        return parent::update($request, $id);
    }

    #[Route('/{id}/delete', name: 'list_delete', methods: ['POST'])]
    public function delete(Request $request, string $id): Response
    {
        return parent::delete($request, $id);
    }

    #[Route('/bulk', name: 'list_bulk', methods: ['POST'])]
    public function bulkDelete(Request $request): Response
    {
        return parent::bulkDelete($request);
    }

    #[Route('/export', name: 'list_export', methods: ['GET'])]
    public function export(Request $request): Response
    {
        return parent::export($request);
    }
}
