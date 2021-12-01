<?php

namespace FormEntries\Tests;

use FormEntries\Forms\Form;
use FormEntries\Forms\UniversalForm;

class TypesMapTest extends TestCase
{

    /** @test */
    public function form_has_types()
    {
        Form::typesMap([
            'uni' => UniversalForm::class,
        ]);

        $this->assertEquals(UniversalForm::class, UniversalForm::getClassByType('uni'));
        $this->assertEquals(UniversalForm::class, Form::getClassByType('uni'));

        $form = new UniversalForm();
        $this->assertEquals('uni', $form::getType());
    }

    /** @test */
    public function form_has_return_class_if_no_type()
    {
        Form::$typesMap = [];

        $this->assertNull(UniversalForm::getClassByType('foo'));
        $this->assertNull(Form::getClassByType('uni'));

        $form = new UniversalForm();
        $this->assertEquals(UniversalForm::class, $form::getType());
    }
}
