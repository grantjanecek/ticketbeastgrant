<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\RandomOrderConfirmationNumberGenerator;

class RandomOrderConfirmationNumberGeneratorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->generator = new RandomOrderConfirmationNumberGenerator;
    }
    
    /** @test */
    public function must_be_24_characters_long()
    {
        $confirmationNumber = $this->generator->generate();

        $this->assertEquals(24, strlen($confirmationNumber));
    }

    /** @test */
    public function must_contain_only_uppercase_and_numbers()
    {
        $confirmationNumber = $this->generator->generate();

        $this->assertMatchesRegularExpression('/^[A-Z0-9]+$/', $confirmationNumber);
    }

    /** @test */
    public function must_not_contain_ambiguous_characters()
    {
        $confirmationNumber = $this->generator->generate();

        $this->assertFalse(strpos($confirmationNumber, '1'));
        $this->assertFalse(strpos($confirmationNumber, 'I'));
        $this->assertFalse(strpos($confirmationNumber, '0'));
        $this->assertFalse(strpos($confirmationNumber, 'O'));
    }

    /** @test */
    public function confirmation_numbers_are_unique()
    {
        $confirmationNumbers = array_map(fn () => $this->generator->generate(), range(1, 100));

        $this->assertCount(100, array_unique($confirmationNumbers));
    }
}
