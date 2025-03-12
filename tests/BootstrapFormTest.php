<?php

use Watson\BootstrapForm\BootstrapForm;

use LaravelLux\Html\FormBuilder;
use LaravelLux\Html\HtmlBuilder;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Eloquent\Database\Model;
use PHPUnit\Framework\TestCase;

class BootstrapFormTest extends TestCase
{
    protected $bootstrapForm;

    protected $htmlBuilderMock;

    protected $formBuidlerMock;

    protected $configMock;

    public function setUp(): void
    {
        $this->htmlBuilderMock = Mockery::mock(HtmlBuilder::class);
        $this->formBuidlerMock = Mockery::mock(FormBuilder::class);
        $this->configMock = Mockery::mock(Repository::class)->shouldDeferMissing();

        $this->bootstrapForm = new BootstrapForm(
            $this->htmlBuilderMock,
            $this->formBuidlerMock,
            $this->configMock,
        );
    }

    /** @test */
    public function it_opens_default_form()
    {
        $this->formBuidlerMock->shouldReceive('open')->once()->with([
            'role' => 'form',
            'class' => 'form-horizontal'
        ])->andReturn('foo');

        $this->configMock->shouldReceive('get')
            ->with('bootstrap_form.type')
            ->once()
            ->andReturn('form-horizontal');

        $result = $this->bootstrapForm->open();

        $this->assertEquals('foo', $result);
    }

    /** @test */
    public function it_opens_store_model_form()
    {
        $model = Mockery::mock(Model::class);
        $model->exists = false;

        $this->formBuidlerMock->shouldReceive('model')
            ->once()
            ->with($model, [
                'role' => 'form',
                'route' => 'bar',
                'method' => 'POST',
                'class' => 'form-horizontal',
            ])
            ->andReturn('foo');

        $this->configMock->shouldReceive('get')
            ->with('bootstrap_form.type')
            ->once()
            ->andReturn('form-horizontal');

        $result = $this->bootstrapForm->open([
            'model' => $model,
            'store' => 'bar',
            'update' => 'baz'
        ]);

        $this->assertEquals('foo', $result);
    }

    /** @test */
    public function it_opens_update_model_form()
    {
        $model = Mockery::mock(Model::class);
        $model->exists = true;

        $model->shouldReceive('getRouteKey')
            ->once()
            ->andReturn(1);

        $this->formBuidlerMock->shouldReceive('model')
            ->once()
            ->with($model, [
                'role' => 'form',
                'route' => ['baz', 1],
                'method' => 'PUT',
                'class' => 'form-horizontal',
            ])
            ->andReturn('foo');

        $this->configMock->shouldReceive('get')
            ->with('bootstrap_form.type')
            ->once()
            ->andReturn('form-horizontal');

        $result = $this->bootstrapForm->open([
            'model' => $model,
            'store' => 'bar',
            'update' => 'baz'
        ]);

        $this->assertEquals('foo', $result);
    }

    /** @test */
    public function it_opens_a_vertical_form()
    {
        $this->formBuidlerMock->shouldReceive('open')
            ->with([
                'role' => 'form',
                'class' => '',
            ])
            ->once()
            ->andReturn('foo');

        $result = $this->bootstrapForm->vertical();

        $this->assertEquals('foo', $result);
    }

    /** @test */
    public function it_opens_an_inline_form()
    {
        $this->formBuidlerMock->shouldReceive('open')
            ->with([
                'class' => 'form-inline',
                'role' => 'form'
            ])
            ->once()
            ->andReturn('foo');

        $result = $this->bootstrapForm->inline();

        $this->assertEquals('foo', $result);
    }

    /** @test */
    public function it_opens_a_horizontal_form()
    {
        $this->formBuidlerMock->shouldReceive('open')
            ->with([
                'class' => 'form-horizontal',
                'role' => 'form'
            ])
            ->once()
            ->andReturn('foo');

        $result = $this->bootstrapForm->horizontal();

        $this->assertEquals('foo', $result);
    }

    /** @test */
    public function it_closes_a_form()
    {
        $this->formBuidlerMock->shouldReceive('close')->once()->andReturn('foo');

        $result = $this->bootstrapForm->close();

        $this->assertEquals('foo', $result);
    }

    /** @test */
    public function it_returns_normal_field_names()
    {
        $result = $this->bootstrapForm->flattenFieldName('foo');

        $this->assertEquals('foo', $result);
    }

    /** @test */
    public function it_removes_empty_array_from_field_name()
    {
        $result = $this->bootstrapForm->flattenFieldName('foo[]');

        $this->assertEquals('foo', $result);
    }

    /** @test */
    public function it_flattens_array_from_field_name()
    {
        $result = $this->bootstrapForm->flattenFieldName('foo[bar]');

        $this->assertEquals('foo.bar', $result);
    }

    /** @test */
    public function in_allows_zero_in_field_name()
    {
        $result = $this->bootstrapForm->flattenFieldName('foo[0]');

        $this->assertEquals('foo.0', $result);
    }

    /** @test */
    public function it_flattens_nested_array_from_field_name()
    {
        $result = $this->bootstrapForm->flattenFieldName('foo[bar][baz]');

        $this->assertEquals('foo.bar.baz', $result);
    }
}
