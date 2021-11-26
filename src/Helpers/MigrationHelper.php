<?php


namespace FormEntries\Helpers;

use Illuminate\Database\Schema\Blueprint;

class MigrationHelper
{
    public static function defaultColumns(Blueprint $table)
    {
        $table->id();
        $table->string('type', 50)->default('default')->index();
        $table->string('content_type')->nullable()->index();
        $table->json('content')->nullable();
        $table->dateTime('notified_at')->nullable()->index();
        $table->nullableMorphs('sender');
        $table->json('meta')->nullable();
        $table->timestamps();
    }
}
