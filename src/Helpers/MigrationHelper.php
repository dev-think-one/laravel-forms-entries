<?php


namespace FormEntries\Helpers;

use Illuminate\Database\Schema\Blueprint;

class MigrationHelper
{
    public static function defaultColumns(Blueprint $table)
    {
        $table->id();
        $table->string('type', 50)->default('default')->index();
        $table->dateTime('is_notified')->nullable();
        $table->string('class_name')->nullable();
        $table->longText('content')->nullable();
        $table->morphs('sender');
        $table->mediumText('meta')->nullable();
        $table->timestamps();
    }
}
