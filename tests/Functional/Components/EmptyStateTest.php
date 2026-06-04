<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Functional\Components;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

final class EmptyStateTest extends TestCase
{
    #[Test]
    public function itRendersTitleAndActionLink(): void
    {
        $output = $this->render([
            'title' => 'No users found',
            'action_label' => 'Create user',
            'action_url' => '/admin/users/new',
        ]);

        $this->assertStringContainsString('No users found', $output);
        $this->assertStringContainsString('Create user', $output);
        $this->assertStringContainsString('href="/admin/users/new"', $output);
    }

    #[Test]
    public function itRendersAnIconSvg(): void
    {
        $output = $this->render([
            'title' => 'No users found',
        ]);

        $this->assertStringContainsString('<svg', $output);
        $this->assertStringContainsString('</svg>', $output);
        $this->assertStringContainsString('beacon-empty-state-component__icon', $output);
    }

    #[Test]
    public function itRendersDescriptionWhenProvided(): void
    {
        $output = $this->render([
            'title' => 'No users found',
            'description' => 'Try creating the first user.',
        ]);

        $this->assertStringContainsString('Try creating the first user.', $output);
        $this->assertStringContainsString('beacon-empty-state-component__description', $output);
    }

    #[Test]
    public function itRendersActionIconWhenProvided(): void
    {
        $icon = '<svg><line x1="12" y1="5" x2="12" y2="19"/></svg>';

        $output = $this->render([
            'title' => 'No users found',
            'action_label' => 'Create user',
            'action_url' => '/admin/users/new',
            'action_icon' => $icon,
        ]);

        $this->assertStringContainsString($icon, $output);
        $this->assertStringContainsString('beacon-empty-state-component__action-icon', $output);
    }

    #[Test]
    public function itUsesProvidedCustomIcon(): void
    {
        $customIcon = '<svg id="custom-empty-icon"></svg>';

        $output = $this->render([
            'title' => 'No products found',
            'icon' => $customIcon,
        ]);

        $this->assertStringContainsString('id="custom-empty-icon"', $output);
    }

    #[Test]
    public function itOmitsActionWhenLabelOrUrlMissing(): void
    {
        $output = $this->render([
            'title' => 'No users found',
        ]);

        $this->assertStringNotContainsString('beacon-empty-state-component__action', $output);
    }

    /** @param array<string, mixed> $context */
    private function render(array $context): string
    {
        $bundleRoot = dirname(__DIR__, 3);
        $loader = new \Twig\Loader\FilesystemLoader();
        $loader->addPath($bundleRoot.'/templates', 'BeaconAdmin');

        $twig = new Environment($loader, [
            'strict_variables' => true,
            'autoescape' => 'html',
        ]);

        return $twig->render('@BeaconAdmin/components/empty-state.html.twig', $context);
    }
}
