<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud\Form;

use Symfony\Component\Form\FormInterface;
use Twig\Environment;

class FormRenderer
{
    public function __construct(
        protected Environment $twig,
    ) {
    }

    /** @param FormInterface<mixed> $form */
    public function render(FormInterface $form, string $action, string $entityLabel): string
    {
        return $this->twig->render('@BeaconAdmin/crud/form.html.twig', [
            'form' => $form->createView(),
            'action' => $action,
            'entityLabel' => $entityLabel,
        ]);
    }
}
