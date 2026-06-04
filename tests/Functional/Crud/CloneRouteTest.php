<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Functional\Crud;

use Devgeek\BeaconAdmin\Controller\AbstractCrudController;
use Devgeek\BeaconAdmin\Tests\Fixtures\TestApp\Entity\ClonePost;
use Devgeek\BeaconAdmin\Tests\Fixtures\TestApp\TestKernel;
use Devgeek\BeaconAdmin\Tests\Functional\BeaconWebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class CloneRouteTest extends BeaconWebTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    /**
     * @return array{0: \Symfony\Bundle\FrameworkBundle\KernelBrowser, 1: EntityManagerInterface}
     */
    private function bootKernelWithSchema(): array
    {
        $client = static::createClient();
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');
        \assert($em instanceof EntityManagerInterface);

        $schemaTool = new SchemaTool($em);
        $schemaTool->createSchema([
            $em->getClassMetadata(ClonePost::class),
        ]);

        return [$client, $em];
    }

    public function testCloneRouteIsRegisteredWithPostMethod(): void
    {
        $refl = new \ReflectionMethod(AbstractCrudController::class, 'clone');
        $attributes = $refl->getAttributes(Route::class);

        $this->assertCount(1, $attributes);
        $route = $attributes[0]->newInstance();
        $this->assertSame('/{id}/clone', $route->path);
        $this->assertSame(['POST'], $route->methods);
        $this->assertSame('clone', $route->name);
    }

    public function testPostClonesEntityAndRedirectsToEdit(): void
    {
        [$client, $em] = $this->bootKernelWithSchema();

        $this->authenticate($client);

        $post = new ClonePost();
        $post->setName('To be cloned');
        $post->setSlug('to-be-cloned');
        $em->persist($post);
        $em->flush();
        $em->clear();

        $sourceId = $post->getId();
        $this->assertNotNull($sourceId);

        $token = $this->generateCsrfToken($client, $sourceId, 'clone'.$sourceId);

        $client->request(
            Request::METHOD_POST,
            sprintf('/admin/clone-posts/%d/clone', $sourceId),
            ['_token' => $token],
        );

        $this->assertResponseRedirects(
            sprintf('/admin/clone-posts/%d/edit', $sourceId + 1),
            \Symfony\Component\HttpFoundation\Response::HTTP_FOUND,
        );

        $em->clear();
        $all = $em->getRepository(ClonePost::class)->findAll();
        $this->assertCount(2, $all);

        $clone = $em->getRepository(ClonePost::class)->find($sourceId + 1);
        $this->assertNotNull($clone);
        $this->assertSame('To be cloned', $clone->getName());
        $this->assertSame('to-be-cloned', $clone->getSlug());
    }

    public function testPostWithoutCsrfTokenIsForbidden(): void
    {
        [$client, $em] = $this->bootKernelWithSchema();

        $this->authenticate($client);

        $post = new ClonePost();
        $post->setName('Untouchable');
        $post->setSlug('untouchable');
        $em->persist($post);
        $em->flush();
        $em->clear();

        $client->request(
            Request::METHOD_POST,
            sprintf('/admin/clone-posts/%d/clone', $post->getId()),
        );

        $this->assertResponseStatusCodeSame(\Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN);
    }

    public function testGetIsMethodNotAllowed(): void
    {
        [$client, $em] = $this->bootKernelWithSchema();

        $this->authenticate($client);

        $post = new ClonePost();
        $post->setName('Get attempt');
        $post->setSlug('get-attempt');
        $em->persist($post);
        $em->flush();

        $client->request(
            Request::METHOD_GET,
            sprintf('/admin/clone-posts/%d/clone', $post->getId()),
        );

        $this->assertResponseStatusCodeSame(\Symfony\Component\HttpFoundation\Response::HTTP_METHOD_NOT_ALLOWED);
    }

    private function authenticate(\Symfony\Bundle\FrameworkBundle\KernelBrowser $client): void
    {
        $user = new \Symfony\Component\Security\Core\User\InMemoryUser(
            'admin_user',
            'admin_pass',
            ['ROLE_ADMIN'],
        );

        $client->loginUser($user);
    }

    private function generateCsrfToken(\Symfony\Bundle\FrameworkBundle\KernelBrowser $client, int $entityId, string $intent): string
    {
        $session = $client->getSession();
        if ($session === null) {
            $session = $client->getContainer()->get('session.factory')->createSession();
            $session->start();
        }

        $request = Request::create('/admin');
        $request->setSession($session);
        $request->cookies->set($session->getName(), $session->getId());

        $stack = $client->getContainer()->get('request_stack');
        while ($stack->getCurrentRequest() !== null) {
            $stack->pop();
        }
        $stack->push($request);

        $tokenManager = $client->getContainer()->get('security.csrf.token_manager');
        $token = $tokenManager->getToken($intent)->getValue();
        $session->save();

        $client->getCookieJar()->set(new \Symfony\Component\BrowserKit\Cookie($session->getName(), $session->getId()));

        return $token;
    }
}
