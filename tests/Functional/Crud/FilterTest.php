<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Functional\Crud;

use Devgeek\BeaconAdmin\Tests\Fixtures\TestApp\Entity\FilterTestCategory;
use Devgeek\BeaconAdmin\Tests\Fixtures\TestApp\Entity\FilterTestPost;
use Devgeek\BeaconAdmin\Tests\Fixtures\TestApp\TestKernel;
use Devgeek\BeaconAdmin\Tests\Functional\BeaconWebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;

final class FilterTest extends BeaconWebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $em;

    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $container = $this->client->getContainer();
        $em = $container->get('doctrine.orm.entity_manager');
        \assert($em instanceof EntityManagerInterface);
        $this->em = $em;

        $schemaTool = new SchemaTool($em);
        $schemaTool->dropSchema([]);
        $schemaTool->createSchema([
            $em->getClassMetadata(FilterTestPost::class),
            $em->getClassMetadata(FilterTestCategory::class),
        ]);

        $this->seedData();
        $em->clear();
    }

    private function seedData(): void
    {
        $techCategory = new FilterTestCategory();
        $techCategory->setName('Tech');
        $this->em->persist($techCategory);

        $newsCategory = new FilterTestCategory();
        $newsCategory->setName('News');
        $this->em->persist($newsCategory);

        $lifeCategory = new FilterTestCategory();
        $lifeCategory->setName('Lifestyle');
        $this->em->persist($lifeCategory);

        $this->em->flush();

        $posts = [
            [
                'title' => 'Symfony Tips',
                'isPublished' => true,
                'status' => 'published',
                'publishedAt' => new \DateTimeImmutable('2025-01-15'),
                'category' => $techCategory,
            ],
            [
                'title' => 'Twig Tricks',
                'isPublished' => true,
                'status' => 'published',
                'publishedAt' => new \DateTimeImmutable('2025-02-20'),
                'category' => $techCategory,
            ],
            [
                'title' => 'Daily Update',
                'isPublished' => false,
                'status' => 'draft',
                'publishedAt' => null,
                'category' => $newsCategory,
            ],
            [
                'title' => 'World News',
                'isPublished' => true,
                'status' => 'published',
                'publishedAt' => new \DateTimeImmutable('2025-03-10'),
                'category' => $newsCategory,
            ],
            [
                'title' => 'Wellness Guide',
                'isPublished' => false,
                'status' => 'draft',
                'publishedAt' => null,
                'category' => $lifeCategory,
            ],
        ];

        foreach ($posts as $data) {
            $post = new FilterTestPost();
            $post->setTitle($data['title']);
            $post->setIsPublished($data['isPublished']);
            $post->setStatus($data['status']);
            $post->setPublishedAt($data['publishedAt']);
            $post->setCategory($data['category']);
            $this->em->persist($post);
        }

        $this->em->flush();
    }

    private function listUrl(array $query = []): string
    {
        $base = '/admin/filter-test-posts/';

        return $query === [] ? $base : $base.'?'.http_build_query($query);
    }

    private function getResponse(string $url): Response
    {
        $this->client->request('GET', $url);

        return $this->client->getResponse();
    }

    public function testListPageRendersAllPostsByDefault(): void
    {
        $response = $this->getResponse($this->listUrl());

        $this->assertSame(200, $response->getStatusCode());
        $content = (string) $response->getContent();
        $this->assertStringContainsString('beacon-filters', $content);
        $this->assertSame(5, $this->countTableRows($content));
    }

    public function testBooleanFilterReturnsOnlyPublishedPosts(): void
    {
        $response = $this->getResponse($this->listUrl(['filter' => ['isPublished' => '1']]));

        $this->assertSame(200, $response->getStatusCode());
        $content = (string) $response->getContent();

        $this->assertSame(3, $this->countTableRows($content));
        $this->assertMatchesRegularExpression('/beacon-table__row-checkbox"\s+value="1"/s', $content);
        $this->assertMatchesRegularExpression('/beacon-table__row-checkbox"\s+value="2"/s', $content);
        $this->assertMatchesRegularExpression('/beacon-table__row-checkbox"\s+value="4"/s', $content);
    }

    public function testBooleanFilterReturnsOnlyUnpublishedPosts(): void
    {
        $response = $this->getResponse($this->listUrl(['filter' => ['isPublished' => '0']]));

        $this->assertSame(200, $response->getStatusCode());
        $content = (string) $response->getContent();

        $this->assertSame(2, $this->countTableRows($content));
        $this->assertMatchesRegularExpression('/beacon-table__row-checkbox"\s+value="3"/s', $content);
        $this->assertMatchesRegularExpression('/beacon-table__row-checkbox"\s+value="5"/s', $content);
    }

    public function testChoiceFilterReturnsOnlyDraftPosts(): void
    {
        $response = $this->getResponse($this->listUrl(['filter' => ['status' => 'draft']]));

        $this->assertSame(200, $response->getStatusCode());
        $content = (string) $response->getContent();

        $this->assertSame(2, $this->countTableRows($content));
        $this->assertMatchesRegularExpression('/beacon-table__row-checkbox"\s+value="3"/s', $content);
        $this->assertMatchesRegularExpression('/beacon-table__row-checkbox"\s+value="5"/s', $content);
    }

    public function testChoiceFilterReturnsOnlyPublishedPosts(): void
    {
        $response = $this->getResponse($this->listUrl(['filter' => ['status' => 'published']]));

        $this->assertSame(200, $response->getStatusCode());
        $content = (string) $response->getContent();

        $this->assertSame(3, $this->countTableRows($content));
        $this->assertMatchesRegularExpression('/beacon-table__row-checkbox"\s+value="1"/s', $content);
        $this->assertMatchesRegularExpression('/beacon-table__row-checkbox"\s+value="2"/s', $content);
        $this->assertMatchesRegularExpression('/beacon-table__row-checkbox"\s+value="4"/s', $content);
    }

    public function testDateFilterBetweenReturnsOnlyMatchingPosts(): void
    {
        $response = $this->getResponse($this->listUrl([
            'filter' => [
                'publishedAt' => ['2025-02-01', '2025-03-31'],
            ],
        ]));

        $this->assertSame(200, $response->getStatusCode());
        $content = (string) $response->getContent();

        $this->assertSame(2, $this->countTableRows($content));
        $this->assertMatchesRegularExpression('/beacon-table__row-checkbox"\s+value="2"/s', $content);
        $this->assertMatchesRegularExpression('/beacon-table__row-checkbox"\s+value="4"/s', $content);
    }

    public function testEntityFilterReturnsOnlyPostsInMatchingCategory(): void
    {
        $techCategory = $this->em->getRepository(FilterTestCategory::class)
            ->findOneBy(['name' => 'Tech']);
        \assert($techCategory instanceof FilterTestCategory);
        $techId = $techCategory->getId();
        \assert($techId !== null);

        $response = $this->getResponse($this->listUrl(['filter' => ['category' => (string) $techId]]));

        $this->assertSame(200, $response->getStatusCode());
        $content = (string) $response->getContent();

        $this->assertSame(2, $this->countTableRows($content));
        $this->assertMatchesRegularExpression('/beacon-table__row-checkbox"\s+value="1"/s', $content);
        $this->assertMatchesRegularExpression('/beacon-table__row-checkbox"\s+value="2"/s', $content);
    }

    private function countTableRows(string $content): int
    {
        return preg_match_all('/<tr class="beacon-table__row">/', $content);
    }

    public function testListPageRendersFilterWidgets(): void
    {
        $response = $this->getResponse($this->listUrl());
        $content = (string) $response->getContent();

        $this->assertStringContainsString('beacon-filters__field', $content);
        $this->assertStringContainsString('filter-isPublished', $content);
        $this->assertStringContainsString('filter-status', $content);
        $this->assertStringContainsString('filter-publishedAt-from', $content);
        $this->assertStringContainsString('filter-publishedAt-to', $content);
        $this->assertStringContainsString('filter-category', $content);
    }

    public function testBooleanFilterWidgetHasAnyYesNoRadios(): void
    {
        $response = $this->getResponse($this->listUrl());
        $content = (string) $response->getContent();

        $this->assertStringContainsString('id="filter-isPublished-any"', $content);
        $this->assertStringContainsString('id="filter-isPublished-yes"', $content);
        $this->assertStringContainsString('id="filter-isPublished-no"', $content);
    }

    public function testChoiceFilterWidgetHasExpectedOptions(): void
    {
        $response = $this->getResponse($this->listUrl());
        $content = (string) $response->getContent();

        $this->assertStringContainsString('value="draft"', $content);
        $this->assertStringContainsString('value="published"', $content);
        $this->assertStringContainsString('>Draft<', $content);
        $this->assertStringContainsString('>Published<', $content);
    }

    public function testDateFilterWidgetHasBetweenInputs(): void
    {
        $response = $this->getResponse($this->listUrl());
        $content = (string) $response->getContent();

        $this->assertStringContainsString('name="filter[publishedAt][]"', $content);
        $this->assertStringContainsString('type="date"', $content);
    }

    public function testEntityFilterWidgetLoadsCategoryOptions(): void
    {
        $response = $this->getResponse($this->listUrl());
        $content = (string) $response->getContent();

        $this->assertStringContainsString('>Tech<', $content);
        $this->assertStringContainsString('>News<', $content);
        $this->assertStringContainsString('>Lifestyle<', $content);
    }

    public function testActiveFiltersChipsAppearWhenFilterApplied(): void
    {
        $response = $this->getResponse($this->listUrl(['filter' => ['isPublished' => '1']]));
        $content = (string) $response->getContent();

        $this->assertStringContainsString('beacon-active-filters', $content);
        $this->assertStringContainsString('beacon-active-filters__chip', $content);
        $this->assertStringContainsString('beacon-active-filters__chip-remove', $content);
    }

    public function testActiveFiltersChipsDoNotAppearWithoutFilters(): void
    {
        $response = $this->getResponse($this->listUrl());
        $content = (string) $response->getContent();

        $this->assertStringNotContainsString('beacon-active-filters__chip', $content);
    }

    public function testSelectedBooleanFilterIsMarkedChecked(): void
    {
        $response = $this->getResponse($this->listUrl(['filter' => ['isPublished' => '1']]));
        $content = (string) $response->getContent();

        $this->assertMatchesRegularExpression(
            '/id="filter-isPublished-yes"[^>]*checked/s',
            $content,
        );
    }

    public function testSelectedChoiceFilterIsMarkedSelected(): void
    {
        $response = $this->getResponse($this->listUrl(['filter' => ['status' => 'published']]));
        $content = (string) $response->getContent();

        $this->assertStringContainsString('value="published" selected', $content);
    }

    public function testSelectedEntityFilterIsMarkedSelected(): void
    {
        $techCategory = $this->em->getRepository(FilterTestCategory::class)
            ->findOneBy(['name' => 'Tech']);
        \assert($techCategory instanceof FilterTestCategory);
        $techId = (string) $techCategory->getId();

        $response = $this->getResponse($this->listUrl(['filter' => ['category' => $techId]]));
        $content = (string) $response->getContent();

        $this->assertStringContainsString('value="'.$techId.'" selected', $content);
    }

    public function testDateFilterPreservesValues(): void
    {
        $response = $this->getResponse($this->listUrl([
            'filter' => [
                'publishedAt' => ['2025-02-01', '2025-03-31'],
            ],
        ]));
        $content = (string) $response->getContent();

        $this->assertMatchesRegularExpression('/value="2025-02-01"[^>]*id="filter-publishedAt-from"/s', $content);
        $this->assertMatchesRegularExpression('/value="2025-03-31"[^>]*id="filter-publishedAt-to"/s', $content);
    }
}
