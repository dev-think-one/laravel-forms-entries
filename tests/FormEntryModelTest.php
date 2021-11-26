<?php

namespace FormEntries\Tests;

use FormEntries\Models\FormEntry;
use FormEntries\Tests\Fixtures\Models\User;

class FormEntryModelTest extends TestCase
{
    /** @test */
    public function get_table_from_config()
    {
        $model = new FormEntry();
        $this->assertEquals(config('forms-entries.tables.forms-entries'), $model->getTable());

        config()->set('forms-entries.tables.forms-entries', 'foo_table');
        $this->assertEquals('foo_table', $model->getTable());
    }

    /** @test */
    public function model_has_morph_sender()
    {
        FormEntry::factory()->count(5)->create();
        User::factory()->count(8)->create();
        $user      = User::factory()->create();
        $user      = User::find($user->getKey());
        $formEntry = FormEntry::factory()->for(
            $user,
            'sender'
        )->create();
        FormEntry::factory()->count(5)->create();
        $formEntry->refresh();

        $this->assertInstanceOf(User::class, $formEntry->sender);
        $this->assertEquals($user->getKey(), $formEntry->sender->getKey());
    }
}
