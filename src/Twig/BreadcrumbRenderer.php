<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Twig;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

final readonly class BreadcrumbRenderer
{
    public function __construct(
        private RequestStack $requestStack,
        private RouterInterface $router,
        private AdminRuntime $adminRuntime,
    ) {
    }

    /** @return array<array{label: string, url: ?string}> */
    public function build(): array
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            return [];
        }

        $route = (string) $request->attributes->get('_route', '');

        if ('' === $route) {
            return $this->buildFromPath($request);
        }

        $menuTrail = $this->buildFromMenu($route, $request);

        if ([] !== $menuTrail) {
            return $menuTrail;
        }

        return $this->buildFromPath($request);
    }

    /** @return array<array{label: string, url: ?string}> */
    private function buildFromMenu(string $route, Request $request): array
    {
        $menu = $this->adminRuntime->getMenu();

        if ([] === $menu) {
            return [];
        }

        $trail = $this->findMenuPath($menu, $route);

        if (null === $trail) {
            return [];
        }

        $items = [];

        foreach ($trail as $index => $entry) {
            $isLast = $index === count($trail) - 1;
            $items[] = [
                'label' => $entry['label'],
                'url' => $isLast ? null : $this->urlForRoute($entry['route'] ?? null, $request),
            ];
        }

        return $items;
    }

    /**
     * @param array<array{label: string, route: ?string, icon: ?string, role: ?string, children: array<mixed>}> $menu
     *
     * @return array<array{label: string, route: ?string}>|null
     */
    private function findMenuPath(array $menu, string $route): ?array
    {
        foreach ($menu as $item) {
            if (($item['route'] ?? null) === $route) {
                return [['label' => $item['label'], 'route' => $item['route']]];
            }

            $children = $item['children'];

            if ([] !== $children) {
                $childPath = $this->findMenuPath($children, $route);
                if (null !== $childPath) {
                    return array_merge(
                        [['label' => $item['label'], 'route' => $item['route']]],
                        $childPath,
                    );
                }
            }
        }

        return null;
    }

    /** @return array<array{label: string, url: ?string}> */
    private function buildFromPath(Request $request): array
    {
        $path = trim($request->getPathInfo(), '/');

        if ('' === $path) {
            return [];
        }

        $segments = array_values(array_filter(explode('/', $path), static fn (string $s): bool => '' !== $s));
        $count = count($segments);
        $items = [];

        foreach ($segments as $index => $segment) {
            $isLast = $index === $count - 1;
            $items[] = [
                'label' => $this->humanize($segment),
                'url' => $isLast ? null : $this->urlForPath($segments, $index + 1),
            ];
        }

        return $items;
    }

    /** @param array<string> $segments */
    private function urlForPath(array $segments, int $count): string
    {
        $prefix = (string) ($this->adminRuntime->getConfig('route_prefix') ?? '/admin');
        $normalizedPrefix = trim($prefix, '/');
        $prefixSegments = '' === $normalizedPrefix ? [] : explode('/', $normalizedPrefix);
        $prefixCount = count($prefixSegments);

        $pathStartsWithPrefix = $prefixCount > 0
            && count($segments) >= $prefixCount
            && array_slice($segments, 0, $prefixCount) === $prefixSegments;

        $offset = $pathStartsWithPrefix ? $prefixCount : 0;
        $length = max(0, $count - $offset);
        $pathSegments = array_slice($segments, $offset, $length);
        $cleanPrefix = rtrim($prefix, '/');

        if ([] === $pathSegments) {
            return '' === $cleanPrefix ? '/' : $cleanPrefix;
        }

        return ('' === $cleanPrefix ? '' : $cleanPrefix.'/').implode('/', $pathSegments);
    }

    private function urlForRoute(?string $route, Request $request): ?string
    {
        if (null === $route) {
            return null;
        }

        try {
            $url = $this->router->generate($route, $request->query->all());
        } catch (\Throwable) {
            return null;
        }

        return $url;
    }

    private function humanize(string $segment): string
    {
        $cleaned = preg_replace(['/[-_]+/', '/(?<=\d)(?=[A-Za-z])|(?<=[A-Za-z])(?=\d)/'], ' ', $segment);
        $spaced = is_string($cleaned) ? $cleaned : $segment;
        $spaced = trim($spaced);

        if ('' === $spaced) {
            return $segment;
        }

        return ucwords($spaced);
    }
}
