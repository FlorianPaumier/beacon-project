<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Form\Component;

use Devgeek\BeaconAdmin\Support\Component;

class FormBuilder
{
    /** @var array<Component> */
    protected array $components = [];

    public static function make(): static
    {
        return new static();
    }

    /** @return array<Component> */
    public function all(): array
    {
        return $this->components;
    }

    public function addText(string $name, ?string $label = null, ?\Closure $callback = null): static
    {
        $input = TextInput::make()->name($name);

        if ($label !== null) {
            $input->label($label);
        }

        if ($callback !== null) {
            $callback($input);
        }

        $this->components[] = $input;

        return $this;
    }

    public function addTextarea(string $name, ?string $label = null, ?\Closure $callback = null): static
    {
        $input = Textarea::make()->name($name);

        if ($label !== null) {
            $input->label($label);
        }

        if ($callback !== null) {
            $callback($input);
        }

        $this->components[] = $input;

        return $this;
    }

    public function addNumber(string $name, ?string $label = null, ?\Closure $callback = null): static
    {
        $input = Number::make()->name($name);

        if ($label !== null) {
            $input->label($label);
        }

        if ($callback !== null) {
            $callback($input);
        }

        $this->components[] = $input;

        return $this;
    }

    public function addEmail(string $name, ?string $label = null, ?\Closure $callback = null): static
    {
        $input = Email::make()->name($name);

        if ($label !== null) {
            $input->label($label);
        }

        if ($callback !== null) {
            $callback($input);
        }

        $this->components[] = $input;

        return $this;
    }

    public function addPassword(string $name, ?string $label = null, ?\Closure $callback = null): static
    {
        $input = Password::make()->name($name);

        if ($label !== null) {
            $input->label($label);
        }

        if ($callback !== null) {
            $callback($input);
        }

        $this->components[] = $input;

        return $this;
    }

    public function addSelect(string $name, ?string $label = null, ?\Closure $callback = null): static
    {
        $input = Select::make()->name($name);

        if ($label !== null) {
            $input->label($label);
        }

        if ($callback !== null) {
            $callback($input);
        }

        $this->components[] = $input;

        return $this;
    }

    public function addCheckbox(string $name, ?string $label = null, ?\Closure $callback = null): static
    {
        $input = Checkbox::make()->name($name);

        if ($label !== null) {
            $input->label($label);
        }

        if ($callback !== null) {
            $callback($input);
        }

        $this->components[] = $input;

        return $this;
    }

    public function addToggle(string $name, ?string $label = null, ?\Closure $callback = null): static
    {
        $input = Toggle::make()->name($name);

        if ($label !== null) {
            $input->label($label);
        }

        if ($callback !== null) {
            $callback($input);
        }

        $this->components[] = $input;

        return $this;
    }

    public function addDate(string $name, ?string $label = null, ?\Closure $callback = null): static
    {
        $input = Date::make()->name($name);

        if ($label !== null) {
            $input->label($label);
        }

        if ($callback !== null) {
            $callback($input);
        }

        $this->components[] = $input;

        return $this;
    }

    public function addDateTime(string $name, ?string $label = null, ?\Closure $callback = null): static
    {
        $input = DateTime::make()->name($name);

        if ($label !== null) {
            $input->label($label);
        }

        if ($callback !== null) {
            $callback($input);
        }

        $this->components[] = $input;

        return $this;
    }

    public function addTime(string $name, ?string $label = null, ?\Closure $callback = null): static
    {
        $input = Time::make()->name($name);

        if ($label !== null) {
            $input->label($label);
        }

        if ($callback !== null) {
            $callback($input);
        }

        $this->components[] = $input;

        return $this;
    }

    public function addFile(string $name, ?string $label = null, ?\Closure $callback = null): static
    {
        $input = File::make()->name($name);

        if ($label !== null) {
            $input->label($label);
        }

        if ($callback !== null) {
            $callback($input);
        }

        $this->components[] = $input;

        return $this;
    }

    public function addColor(string $name, ?string $label = null, ?\Closure $callback = null): static
    {
        $input = Color::make()->name($name);

        if ($label !== null) {
            $input->label($label);
        }

        if ($callback !== null) {
            $callback($input);
        }

        $this->components[] = $input;

        return $this;
    }

    public function addUrl(string $name, ?string $label = null, ?\Closure $callback = null): static
    {
        $input = Url::make()->name($name);

        if ($label !== null) {
            $input->label($label);
        }

        if ($callback !== null) {
            $callback($input);
        }

        $this->components[] = $input;

        return $this;
    }

    public function addRadio(string $name, ?string $label = null, ?\Closure $callback = null): static
    {
        $input = Radio::make()->name($name);

        if ($label !== null) {
            $input->label($label);
        }

        if ($callback !== null) {
            $callback($input);
        }

        $this->components[] = $input;

        return $this;
    }

    public function addHidden(string $name, ?\Closure $callback = null): static
    {
        $input = Hidden::make()->name($name);

        if ($callback !== null) {
            $callback($input);
        }

        $this->components[] = $input;

        return $this;
    }

    public function addRange(string $name, ?string $label = null, ?\Closure $callback = null): static
    {
        $input = Range::make()->name($name);

        if ($label !== null) {
            $input->label($label);
        }

        if ($callback !== null) {
            $callback($input);
        }

        $this->components[] = $input;

        return $this;
    }

    public function addTel(string $name, ?string $label = null, ?\Closure $callback = null): static
    {
        $input = Tel::make()->name($name);

        if ($label !== null) {
            $input->label($label);
        }

        if ($callback !== null) {
            $callback($input);
        }

        $this->components[] = $input;

        return $this;
    }

    public function addSearch(string $name, ?string $label = null, ?\Closure $callback = null): static
    {
        $input = Search::make()->name($name);

        if ($label !== null) {
            $input->label($label);
        }

        if ($callback !== null) {
            $callback($input);
        }

        $this->components[] = $input;

        return $this;
    }

    public function addAssociation(string $name, ?string $label = null, ?\Closure $callback = null): static
    {
        $input = Association::make()->name($name);

        if ($label !== null) {
            $input->label($label);
        }

        if ($callback !== null) {
            $callback($input);
        }

        $this->components[] = $input;

        return $this;
    }

    public function addFieldset(?string $label = null, ?\Closure $callback = null): static
    {
        $input = Fieldset::make();

        if ($label !== null) {
            $input->label($label);
        }

        if ($callback !== null) {
            $callback($input);
        }

        $this->components[] = $input;

        return $this;
    }

    public function addKeyValue(string $name, ?string $label = null, ?\Closure $callback = null): static
    {
        $input = KeyValue::make()->name($name);

        if ($label !== null) {
            $input->label($label);
        }

        if ($callback !== null) {
            $callback($input);
        }

        $this->components[] = $input;

        return $this;
    }

    public function addTags(string $name, ?string $label = null, ?\Closure $callback = null): static
    {
        $input = Tags::make()->name($name);

        if ($label !== null) {
            $input->label($label);
        }

        if ($callback !== null) {
            $callback($input);
        }

        $this->components[] = $input;

        return $this;
    }

    public function addRepeater(string $name, ?string $label = null, ?\Closure $callback = null): static
    {
        $input = Repeater::make()->name($name);

        if ($label !== null) {
            $input->label($label);
        }

        if ($callback !== null) {
            $callback($input);
        }

        $this->components[] = $input;

        return $this;
    }
}
