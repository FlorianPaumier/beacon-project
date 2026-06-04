<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Functional\Crud;

use Devgeek\BeaconAdmin\Crud\Action\CloneAction;
use Devgeek\BeaconAdmin\Crud\Event\AfterCreateEvent;
use Devgeek\BeaconAdmin\Crud\Event\BeforeCreateEvent;
use Devgeek\BeaconAdmin\Tests\Fixtures\TestApp\Entity\CloneCategory;
use Devgeek\BeaconAdmin\Tests\Fixtures\TestApp\Entity\ClonePost;
use Devgeek\BeaconAdmin\Tests\Fixtures\TestApp\Entity\CloneTag;
use Devgeek\BeaconAdmin\Tests\Fixtures\TestApp\TestKernel;
use Devgeek\BeaconAdmin\Tests\Functional\BeaconWebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class CloneActionTest extends BeaconWebTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    /**
     * @return array{0: EntityManagerInterface, 1: EventDispatcherInterface}
     */
    private function bootKernelWithSchema(): array
    {
        $client = static::createClient();
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');
        \assert($em instanceof EntityManagerInterface);

        $schemaTool = new SchemaTool($em);
        $schemaTool->createSchema([
            $em->getClassMetadata(ClonePost::class),
            $em->getClassMetadata(CloneCategory::class),
            $em->getClassMetadata(CloneTag::class),
        ]);

        $dispatcher = $client->getContainer()->get('event_dispatcher');
        \assert($dispatcher instanceof EventDispatcherInterface);

        return [$em, $dispatcher];
    }

    public function testCloneProducesNewEntityWithNewId(): void
    {
        [$em, $dispatcher] = $this->bootKernelWithSchema();

        $source = $this->seedPost($em, 'Original');

        $clone = CloneAction::make()->clone($source, $em, $dispatcher);

        $this->assertNotNull($clone->getId());
        $this->assertNotSame($source->getId(), $clone->getId());
        $this->assertNotSame($source, $clone);
    }

    public function testScalarFieldsAreCopied(): void
    {
        [$em, $dispatcher] = $this->bootKernelWithSchema();

        $source = $this->seedPost($em, 'Copyable');
        $em->clear();

        $reloaded = $em->getRepository(ClonePost::class)->find($source->getId());
        $this->assertNotNull($reloaded);

        $clone = CloneAction::make()->clone($reloaded, $em, $dispatcher);

        $this->assertSame('Copyable', $clone->getName());
        $this->assertSame($reloaded->getSlug(), $clone->getSlug());
    }

    public function testCreatedAtIsReset(): void
    {
        [$em, $dispatcher] = $this->bootKernelWithSchema();

        $source = $this->seedPost($em, 'Timed');

        $past = new \DateTimeImmutable('2020-01-01T00:00:00+00:00');
        $reflection = new \ReflectionProperty(ClonePost::class, 'createdAt');
        $reflection->setValue($source, $past);
        $em->flush();
        $em->clear();

        $reloaded = $em->getRepository(ClonePost::class)->find($source->getId());
        $this->assertNotNull($reloaded);

        $before = new \DateTimeImmutable();
        $clone = CloneAction::make()->clone($reloaded, $em, $dispatcher);
        $after = new \DateTimeImmutable();

        $this->assertGreaterThanOrEqual($before, $clone->getCreatedAt());
        $this->assertLessThanOrEqual($after, $clone->getCreatedAt());
        $this->assertNotEquals($past->getTimestamp(), $clone->getCreatedAt()->getTimestamp());
    }

    public function testToOneAssociationIsCopied(): void
    {
        [$em, $dispatcher] = $this->bootKernelWithSchema();

        $category = new CloneCategory();
        $category->setName('News');
        $em->persist($category);

        $source = $this->seedPost($em, 'Categorised');
        $source->setCategory($category);
        $em->flush();
        $em->clear();

        $reloaded = $em->getRepository(ClonePost::class)->find($source->getId());
        $this->assertNotNull($reloaded);

        $clone = CloneAction::make()->clone($reloaded, $em, $dispatcher);

        $this->assertNotNull($clone->getCategory());
        $this->assertSame($category->getId(), $clone->getCategory()->getId());
        $this->assertSame('News', $clone->getCategory()->getName());
    }

    public function testToManyCollectionsAreEmptyOnClone(): void
    {
        [$em, $dispatcher] = $this->bootKernelWithSchema();

        $tag = new CloneTag();
        $tag->setName('urgent');
        $em->persist($tag);

        $source = $this->seedPost($em, 'Tagged');
        $source->addTag($tag);
        $em->flush();
        $em->clear();

        $reloaded = $em->getRepository(ClonePost::class)->find($source->getId());
        $this->assertNotNull($reloaded);
        $this->assertCount(1, $reloaded->getTags());

        $clone = CloneAction::make()->clone($reloaded, $em, $dispatcher);

        $this->assertCount(0, $clone->getTags());
    }

    public function testCreateEventsAreDispatched(): void
    {
        [$em, $dispatcher] = $this->bootKernelWithSchema();

        $source = $this->seedPost($em, 'Evented');

        $beforeFired = false;
        $afterFired = false;
        $capturedBefore = null;
        $capturedAfter = null;

        $dispatcher->addListener(BeforeCreateEvent::class, static function (BeforeCreateEvent $event) use (&$beforeFired, &$capturedBefore): void {
            $beforeFired = true;
            $capturedBefore = $event->getEntity();
        });
        $dispatcher->addListener(AfterCreateEvent::class, static function (AfterCreateEvent $event) use (&$afterFired, &$capturedAfter): void {
            $afterFired = true;
            $capturedAfter = $event->getEntity();
        });

        $clone = CloneAction::make()->clone($source, $em, $dispatcher);

        $this->assertTrue($beforeFired, 'BeforeCreateEvent should be dispatched');
        $this->assertTrue($afterFired, 'AfterCreateEvent should be dispatched');
        $this->assertSame($clone, $capturedBefore);
        $this->assertSame($clone, $capturedAfter);
    }

    private function seedPost(EntityManagerInterface $em, string $name): ClonePost
    {
        $post = new ClonePost();
        $post->setName($name);
        $post->setSlug(strtolower($name).'-'.uniqid());
        $em->persist($post);
        $em->flush();

        return $post;
    }
}
