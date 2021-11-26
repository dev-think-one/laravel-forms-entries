<?php

namespace FormEntries\Tests;

use FormEntries\Forms\Form;
use FormEntries\Models\FormEntry;
use FormEntries\Tests\Fixtures\FormEntries\Content\ContactUsFormContent;
use FormEntries\Tests\Fixtures\FormEntries\Forms\ContactUsForm;

class CustomFormTest extends TestCase
{

    /** @test */
    public function contact_us_form()
    {
        Form::typesMap([
            'contact_us' => ContactUsForm::class,
        ]);

        $response = $this->post('testing/public-form', [
            'form_type_field' => 'contact_us',
            'email'           => 'test@foo.bar',
            'foo'             => 'bar',
            'message'         => 'Hello form test.',
        ]);

        /** @var FormEntry $formEntry */
        $formEntry = FormEntry::find($response->json('id'));
        $this->assertNotNull($formEntry);
        $this->assertNull($formEntry->sender);
        $this->assertInstanceOf(ContactUsFormContent::class, $formEntry->content);
        $this->assertNull($formEntry->sender);
        $this->assertEquals('test@foo.bar', $formEntry->content->getAttribute('email'));
        $this->assertEquals('Hello form test.', $formEntry->content->getAttribute('message'));
        $this->assertNull($formEntry->content->getAttribute('foo'));
    }
}
