<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Traits\{CustomerFeatures, UserFeatures, PermissionFeatures};

abstract class TestCase extends BaseTestCase
{
    use CustomerFeatures, UserFeatures, PermissionFeatures;
}
