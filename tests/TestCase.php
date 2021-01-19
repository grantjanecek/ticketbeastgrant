<?php

namespace Tests;

use Illuminate\Testing\Assert;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function setUp() : void
    {
        parent::setUp();

        EloquentCollection::macro('assertContains', fn($model) =>
            Assert::assertTrue($this->contains($model), "Failed asserting that the collection contained the model")
        );

        EloquentCollection::macro('assertNotContains', fn($model) =>
            Assert::assertFalse($this->contains($model), "Failed asserting that the collection contained the model")
        );

        EloquentCollection::macro('assertEquals', function($items){
            Assert::assertEquals(count($this), count($items));
            $this->zip($items)->each(function($pair){
                list($a, $b) = $pair;
                Assert::assertTrue($a->is($b));
            });
        });
    }
}
