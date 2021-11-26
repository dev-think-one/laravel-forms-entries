<?php

namespace FormEntries\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Orchestra\Testbench\Factories\UserFactory;

class User extends \Illuminate\Foundation\Auth\User
{
    use HasFactory, Notifiable;

    protected static function newFactory(): UserFactory
    {
        return new UserFactory();
    }
}
