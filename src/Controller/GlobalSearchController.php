<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Controller;

use Devgeek\BeaconAdmin\Search\GlobalSearchProviderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
class GlobalSearchController extends AbstractController
{
    /** @var iterable<GlobalSearchProviderInterface> */
    private iterable $providers;

    /** @param iterable<GlobalSearchProviderInterface> $providers */
    public function __construct(iterable $providers)
    {
        $this->providers = $providers;
    }

    #[Route('/%beacon_admin.route_prefix%/search', name: 'beacon_admin_global_search', methods: ['GET'])]
    public function search(Request $request): JsonResponse
    {
        $query = $request->query->getString('q', '');

        if ($query === '') {
            return $this->json(['results' => [], 'query' => '']);
        }

        $results = [];

        foreach ($this->providers as $provider) {
            $providerResults = $provider->search($query);
            foreach ($providerResults as $result) {
                $results[] = [
                    'title' => $result['title'],
                    'url' => $result['url'],
                    'description' => $result['description'] ?? '',
                    'provider' => $provider->getLabel(),
                ];
            }
        }

        return $this->json(['results' => $results, 'query' => $query]);
    }
}
