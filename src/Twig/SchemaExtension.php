<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Twig;

use Devgeek\BeaconAdmin\Schema\Schema;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SchemaExtension extends AbstractExtension
{
    /** @return array<TwigFunction> */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('beacon_schema', [$this, 'renderSchema'], ['is_safe' => ['html']]),
        ];
    }

    public function renderSchema(Schema $schema): string
    {
        $output = '';

        foreach ($schema->getComponents() as $index => $component) {
            $frameId = 'beacon-component-'.$index;
            $output .= '<turbo-frame id="'.$frameId.'" class="beacon-schema-component">';
            $output .= $component::class;
            $output .= '</turbo-frame>';
        }

        return $output;
    }
}
