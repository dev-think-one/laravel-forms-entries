<?php

namespace FormEntries\Tests;

use FormEntries\Forms\UniversalFormContent;
use FormEntries\Models\FormEntry;

class FormContentTest extends TestCase
{

    /** @test */
    public function stringify_use_keys_from_package()
    {
        $formEntry   = FormEntry::factory()->create();
        $formContent = new UniversalFormContent($formEntry, [
            'email'        => 'foo@foo.test',
            'second_email' => 'second@foo.test',
            'name'         => '',
            'message'      => null,
        ]);

        $resultString = $formContent->stringify();

        $this->assertStringContainsString("Email: \nfoo@foo.test", $resultString);
        $this->assertStringContainsString("Second email: \nsecond@foo.test", $resultString);
        $this->assertStringContainsString("Name: \n_EMPTY_", $resultString);
        $this->assertStringNotContainsString("name: \n_EMPTY_", $resultString);
        $this->assertStringContainsString("Message: \n_EMPTY_", $resultString);
    }
}
