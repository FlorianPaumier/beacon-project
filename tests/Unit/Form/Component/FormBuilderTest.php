<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Form\Component;

use Devgeek\BeaconAdmin\Form\Component\Association;
use Devgeek\BeaconAdmin\Form\Component\Checkbox;
use Devgeek\BeaconAdmin\Form\Component\Color;
use Devgeek\BeaconAdmin\Form\Component\Date;
use Devgeek\BeaconAdmin\Form\Component\DateTime;
use Devgeek\BeaconAdmin\Form\Component\Email;
use Devgeek\BeaconAdmin\Form\Component\Fieldset;
use Devgeek\BeaconAdmin\Form\Component\File;
use Devgeek\BeaconAdmin\Form\Component\FormBuilder;
use Devgeek\BeaconAdmin\Form\Component\Hidden;
use Devgeek\BeaconAdmin\Form\Component\KeyValue;
use Devgeek\BeaconAdmin\Form\Component\Number;
use Devgeek\BeaconAdmin\Form\Component\Password;
use Devgeek\BeaconAdmin\Form\Component\Radio;
use Devgeek\BeaconAdmin\Form\Component\Range;
use Devgeek\BeaconAdmin\Form\Component\Repeater;
use Devgeek\BeaconAdmin\Form\Component\Search;
use Devgeek\BeaconAdmin\Form\Component\Select;
use Devgeek\BeaconAdmin\Form\Component\Tags;
use Devgeek\BeaconAdmin\Form\Component\Tel;
use Devgeek\BeaconAdmin\Form\Component\Textarea;
use Devgeek\BeaconAdmin\Form\Component\TextInput;
use Devgeek\BeaconAdmin\Form\Component\Time;
use Devgeek\BeaconAdmin\Form\Component\Toggle;
use Devgeek\BeaconAdmin\Form\Component\Url;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class FormBuilderTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $this->assertEmpty(FormBuilder::make()->all());
    }

    #[Test]
    public function itReturnsEmptyArrayByDefault(): void
    {
        $this->assertSame([], FormBuilder::make()->all());
    }

    #[Test]
    public function itAddsTextInput(): void
    {
        $components = FormBuilder::make()
            ->addText('title', 'Title')
            ->all();

        $this->assertCount(1, $components);
        $this->assertInstanceOf(TextInput::class, $components[0]);
        $this->assertSame('title', $components[0]->getName());
        $this->assertSame('Title', $components[0]->getLabel());
    }

    #[Test]
    public function itAddsTextInputWithCallback(): void
    {
        $components = FormBuilder::make()
            ->addText('title', 'Title', fn (TextInput $t) => $t->required()->maxLength(255))
            ->all();

        $this->assertTrue($components[0]->isRequired());
        $this->assertSame(255, $components[0]->getMaxLength());
    }

    #[Test]
    public function itAddsTextarea(): void
    {
        $components = FormBuilder::make()
            ->addTextarea('bio', 'Biography')
            ->all();

        $this->assertInstanceOf(Textarea::class, $components[0]);
        $this->assertSame('bio', $components[0]->getName());
    }

    #[Test]
    public function itAddsTextareaWithCallback(): void
    {
        $components = FormBuilder::make()
            ->addTextarea('bio', 'Bio', fn (Textarea $t) => $t->rows(10)->autoResize())
            ->all();

        $this->assertSame(10, $components[0]->getRows());
        $this->assertTrue($components[0]->isAutoResize());
    }

    #[Test]
    public function itAddsNumber(): void
    {
        $components = FormBuilder::make()
            ->addNumber('price', 'Price')
            ->all();

        $this->assertInstanceOf(Number::class, $components[0]);
        $this->assertSame('price', $components[0]->getName());
    }

    #[Test]
    public function itAddsNumberWithCallback(): void
    {
        $components = FormBuilder::make()
            ->addNumber('price', 'Price', fn (Number $n) => $n->min(0)->max(1000)->step(0.5))
            ->all();

        $this->assertSame(0.0, $components[0]->getMin());
        $this->assertSame(1000.0, $components[0]->getMax());
        $this->assertSame(0.5, $components[0]->getStep());
    }

    #[Test]
    public function itAddsEmail(): void
    {
        $components = FormBuilder::make()
            ->addEmail('contact', 'Contact')
            ->all();

        $this->assertInstanceOf(Email::class, $components[0]);
        $this->assertSame('contact', $components[0]->getName());
    }

    #[Test]
    public function itAddsEmailWithCallback(): void
    {
        $components = FormBuilder::make()
            ->addEmail('contact', 'Contact', fn (Email $e) => $e->multiple())
            ->all();

        $this->assertTrue($components[0]->isMultiple());
    }

    #[Test]
    public function itAddsPassword(): void
    {
        $components = FormBuilder::make()
            ->addPassword('pwd', 'Password')
            ->all();

        $this->assertInstanceOf(Password::class, $components[0]);
        $this->assertSame('pwd', $components[0]->getName());
    }

    #[Test]
    public function itAddsPasswordWithCallback(): void
    {
        $components = FormBuilder::make()
            ->addPassword('pwd', 'Password', fn (Password $p) => $p->showToggle()->maxLength(128))
            ->all();

        $this->assertTrue($components[0]->hasShowToggle());
        $this->assertSame(128, $components[0]->getMaxLength());
    }

    #[Test]
    public function itAddsSelect(): void
    {
        $components = FormBuilder::make()
            ->addSelect('role', 'Role')
            ->all();

        $this->assertInstanceOf(Select::class, $components[0]);
        $this->assertSame('role', $components[0]->getName());
    }

    #[Test]
    public function itAddsSelectWithCallback(): void
    {
        $options = ['admin' => 'Admin', 'user' => 'User'];
        $components = FormBuilder::make()
            ->addSelect('role', 'Role', fn (Select $s) => $s->options($options)->searchable()->multiple())
            ->all();

        $this->assertSame($options, $components[0]->getOptions());
        $this->assertTrue($components[0]->isSearchable());
        $this->assertTrue($components[0]->isMultiple());
    }

    #[Test]
    public function itAddsCheckbox(): void
    {
        $components = FormBuilder::make()
            ->addCheckbox('active', 'Active')
            ->all();

        $this->assertInstanceOf(Checkbox::class, $components[0]);
        $this->assertSame('active', $components[0]->getName());
    }

    #[Test]
    public function itAddsCheckboxWithCallback(): void
    {
        $components = FormBuilder::make()
            ->addCheckbox('active', 'Active', fn (Checkbox $c) => $c->default(true))
            ->all();

        $this->assertTrue($components[0]->isDefault());
    }

    #[Test]
    public function itAddsToggle(): void
    {
        $components = FormBuilder::make()
            ->addToggle('notifications', 'Notifications')
            ->all();

        $this->assertInstanceOf(Toggle::class, $components[0]);
        $this->assertSame('notifications', $components[0]->getName());
    }

    #[Test]
    public function itAddsToggleWithCallback(): void
    {
        $components = FormBuilder::make()
            ->addToggle('notifications', 'Notifications', fn (Toggle $t) => $t->onColor('green'))
            ->all();

        $this->assertSame('green', $components[0]->getOnColor());
    }

    #[Test]
    public function itAddsDate(): void
    {
        $components = FormBuilder::make()
            ->addDate('start', 'Start Date')
            ->all();

        $this->assertInstanceOf(Date::class, $components[0]);
        $this->assertSame('start', $components[0]->getName());
    }

    #[Test]
    public function itAddsDateWithCallback(): void
    {
        $components = FormBuilder::make()
            ->addDate('start', 'Start', fn (Date $d) => $d->min('2024-01-01')->max('2024-12-31'))
            ->all();

        $this->assertSame('2024-01-01', $components[0]->getMin());
        $this->assertSame('2024-12-31', $components[0]->getMax());
    }

    #[Test]
    public function itAddsDateTime(): void
    {
        $components = FormBuilder::make()
            ->addDateTime('created', 'Created At')
            ->all();

        $this->assertInstanceOf(DateTime::class, $components[0]);
        $this->assertSame('created', $components[0]->getName());
    }

    #[Test]
    public function itAddsTime(): void
    {
        $components = FormBuilder::make()
            ->addTime('start', 'Start Time')
            ->all();

        $this->assertInstanceOf(Time::class, $components[0]);
        $this->assertSame('start', $components[0]->getName());
    }

    #[Test]
    public function itAddsFile(): void
    {
        $components = FormBuilder::make()
            ->addFile('avatar', 'Avatar')
            ->all();

        $this->assertInstanceOf(File::class, $components[0]);
        $this->assertSame('avatar', $components[0]->getName());
    }

    #[Test]
    public function itAddsFileWithCallback(): void
    {
        $components = FormBuilder::make()
            ->addFile('doc', 'Document', fn (File $f) => $f->accept('.pdf')->maxSize(1024))
            ->all();

        $this->assertSame('.pdf', $components[0]->getAccept());
        $this->assertSame(1024, $components[0]->getMaxSize());
    }

    #[Test]
    public function itAddsColor(): void
    {
        $components = FormBuilder::make()
            ->addColor('theme', 'Theme Color')
            ->all();

        $this->assertInstanceOf(Color::class, $components[0]);
        $this->assertSame('theme', $components[0]->getName());
    }

    #[Test]
    public function itAddsUrl(): void
    {
        $components = FormBuilder::make()
            ->addUrl('website', 'Website')
            ->all();

        $this->assertInstanceOf(Url::class, $components[0]);
        $this->assertSame('website', $components[0]->getName());
    }

    #[Test]
    public function itAddsUrlWithCallback(): void
    {
        $components = FormBuilder::make()
            ->addUrl('site', 'Site', fn (Url $u) => $u->placeholder('https://'))
            ->all();

        $this->assertSame('https://', $components[0]->getPlaceholder());
    }

    #[Test]
    public function itAddsRadio(): void
    {
        $components = FormBuilder::make()
            ->addRadio('gender', 'Gender')
            ->all();

        $this->assertInstanceOf(Radio::class, $components[0]);
        $this->assertSame('gender', $components[0]->getName());
    }

    #[Test]
    public function itAddsRadioWithCallback(): void
    {
        $components = FormBuilder::make()
            ->addRadio('gender', 'Gender', fn (Radio $r) => $r->options(['m' => 'Male'])->layout('horizontal'))
            ->all();

        $this->assertSame(['m' => 'Male'], $components[0]->getOptions());
        $this->assertSame('horizontal', $components[0]->getLayout());
    }

    #[Test]
    public function itAddsHidden(): void
    {
        $components = FormBuilder::make()
            ->addHidden('token')
            ->all();

        $this->assertInstanceOf(Hidden::class, $components[0]);
        $this->assertSame('token', $components[0]->getName());
    }

    #[Test]
    public function itAddsRange(): void
    {
        $components = FormBuilder::make()
            ->addRange('volume', 'Volume')
            ->all();

        $this->assertInstanceOf(Range::class, $components[0]);
        $this->assertSame('volume', $components[0]->getName());
    }

    #[Test]
    public function itAddsRangeWithCallback(): void
    {
        $components = FormBuilder::make()
            ->addRange('volume', 'Volume', fn (Range $r) => $r->min(0)->max(200)->step(5))
            ->all();

        $this->assertSame(0.0, $components[0]->getMin());
        $this->assertSame(200.0, $components[0]->getMax());
        $this->assertSame(5.0, $components[0]->getStep());
    }

    #[Test]
    public function itAddsTel(): void
    {
        $components = FormBuilder::make()
            ->addTel('phone', 'Phone')
            ->all();

        $this->assertInstanceOf(Tel::class, $components[0]);
        $this->assertSame('phone', $components[0]->getName());
    }

    #[Test]
    public function itAddsTelWithCallback(): void
    {
        $components = FormBuilder::make()
            ->addTel('phone', 'Phone', fn (Tel $t) => $t->pattern('[0-9]+'))
            ->all();

        $this->assertSame('[0-9]+', $components[0]->getPattern());
    }

    #[Test]
    public function itAddsSearch(): void
    {
        $components = FormBuilder::make()
            ->addSearch('q', 'Search')
            ->all();

        $this->assertInstanceOf(Search::class, $components[0]);
        $this->assertSame('q', $components[0]->getName());
    }

    #[Test]
    public function itAddsSearchWithCallback(): void
    {
        $components = FormBuilder::make()
            ->addSearch('q', 'Search', fn (Search $s) => $s->placeholder('Find...'))
            ->all();

        $this->assertSame('Find...', $components[0]->getPlaceholder());
    }

    #[Test]
    public function itAddsAssociation(): void
    {
        $components = FormBuilder::make()
            ->addAssociation('category', 'Category')
            ->all();

        $this->assertInstanceOf(Association::class, $components[0]);
        $this->assertSame('category', $components[0]->getName());
    }

    #[Test]
    public function itAddsAssociationWithCallback(): void
    {
        $components = FormBuilder::make()
            ->addAssociation('tags', 'Tags', fn (Association $a) => $a->targetEntity('App\Entity\Tag')->multiple()->searchable())
            ->all();

        $this->assertSame('App\Entity\Tag', $components[0]->getTargetEntity());
        $this->assertTrue($components[0]->isMultiple());
        $this->assertTrue($components[0]->isSearchable());
    }

    #[Test]
    public function itAddsFieldset(): void
    {
        $components = FormBuilder::make()
            ->addFieldset('Details')
            ->all();

        $this->assertInstanceOf(Fieldset::class, $components[0]);
        $this->assertSame('Details', $components[0]->getLabel());
    }

    #[Test]
    public function itAddsFieldsetWithCallback(): void
    {
        $inner = [TextInput::make()->name('inner')];
        $components = FormBuilder::make()
            ->addFieldset('Details', fn (Fieldset $f) => $f->schema($inner))
            ->all();

        $this->assertSame($inner, $components[0]->getSchema());
    }

    #[Test]
    public function itAddsKeyValue(): void
    {
        $components = FormBuilder::make()
            ->addKeyValue('meta', 'Metadata')
            ->all();

        $this->assertInstanceOf(KeyValue::class, $components[0]);
        $this->assertSame('meta', $components[0]->getName());
    }

    #[Test]
    public function itAddsKeyValueWithCallback(): void
    {
        $components = FormBuilder::make()
            ->addKeyValue('meta', 'Meta', fn (KeyValue $k) => $k->allowAdd(false)->keyPlaceholder('Key'))
            ->all();

        $this->assertFalse($components[0]->isAllowAdd());
        $this->assertSame('Key', $components[0]->getKeyPlaceholder());
    }

    #[Test]
    public function itAddsTags(): void
    {
        $components = FormBuilder::make()
            ->addTags('skills', 'Skills')
            ->all();

        $this->assertInstanceOf(Tags::class, $components[0]);
        $this->assertSame('skills', $components[0]->getName());
    }

    #[Test]
    public function itAddsTagsWithCallback(): void
    {
        $components = FormBuilder::make()
            ->addTags('skills', 'Skills', fn (Tags $t) => $t->maxTags(5)->suggestions(['PHP']))
            ->all();

        $this->assertSame(5, $components[0]->getMaxTags());
        $this->assertSame(['PHP'], $components[0]->getSuggestions());
    }

    #[Test]
    public function itAddsRepeater(): void
    {
        $components = FormBuilder::make()
            ->addRepeater('items', 'Items')
            ->all();

        $this->assertInstanceOf(Repeater::class, $components[0]);
        $this->assertSame('items', $components[0]->getName());
    }

    #[Test]
    public function itAddsRepeaterWithCallback(): void
    {
        $components = FormBuilder::make()
            ->addRepeater('items', 'Items', fn (Repeater $r) => $r->minItems(1)->maxItems(10)->addLabel('Add Item'))
            ->all();

        $this->assertSame(1, $components[0]->getMinItems());
        $this->assertSame(10, $components[0]->getMaxItems());
        $this->assertSame('Add Item', $components[0]->getAddLabel());
    }

    #[Test]
    public function itChainsMultipleComponents(): void
    {
        $components = FormBuilder::make()
            ->addText('title', 'Title', fn (TextInput $t) => $t->required())
            ->addNumber('price', 'Price', fn (Number $n) => $n->min(0))
            ->addEmail('contact', 'Contact')
            ->all();

        $this->assertCount(3, $components);
        $this->assertInstanceOf(TextInput::class, $components[0]);
        $this->assertInstanceOf(Number::class, $components[1]);
        $this->assertInstanceOf(Email::class, $components[2]);
    }
}
