<?php

declare(strict_types=1);

namespace murtaza1904\AvatarGenerator\Tests;

use murtaza1904\AvatarGenerator\AvatarServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            AvatarServiceProvider::class,
        ];
    }
}
