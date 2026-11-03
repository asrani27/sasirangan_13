<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (config('database.default') === 'mysql' && config('database.connections.mysql.database') === 'sasirangan') {
            throw new \RuntimeException('SAFETY ERROR: Tests are strictly forbidden from running against the active MySQL sasirangan database!');
        }
    }
}
