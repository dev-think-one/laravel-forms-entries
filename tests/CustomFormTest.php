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

        $this->assertCount(1, $formEntry->requestIps());
        $this->assertEquals('127.0.0.1', $formEntry->requestIps()[0]);
        $this->assertEquals('127.0.0.1', $formEntry->requestIp());
        $this->assertEquals('Symfony', $formEntry->requestUserAgent());

        $this->assertEquals('Contact Us', $formEntry->name);

        $this->assertEquals(1, FormEntry::query()->content(ContactUsFormContent::class)->count());
        $this->assertEquals(0, FormEntry::query()->content('test')->count());
    }
}
