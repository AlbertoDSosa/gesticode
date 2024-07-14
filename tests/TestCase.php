<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Traits\{CustomerFeatures, UserFeatures};

abstract class TestCase extends BaseTestCase
{
    use CustomerFeatures, UserFeatures;
}
