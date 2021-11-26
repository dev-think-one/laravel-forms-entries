<?php

namespace FormEntries\Tests;

use FormEntries\FormEntryManager;

class FormEntryManagerTest extends TestCase
{
    /** @test */
    public function ignore_migrations()
    {
        $this->assertTrue(FormEntryManager::$runsMigrations);
        $this->assertInstanceOf(FormEntryManager::class, FormEntryManager::ignoreMigrations());
        $this->assertFalse(FormEntryManager::$runsMigrations);
        FormEntryManager::$runsMigrations = true;
    }
}
