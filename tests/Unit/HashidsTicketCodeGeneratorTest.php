<?php

namespace Tests\Unit;

use App\HashidsTicketCodeGenerator;
use App\Models\Ticket;
use PHPUnit\Framework\TestCase;

class HashidsTicketCodeGeneratorTest extends TestCase
{
    /** @test */
    public function ticket_codes_are_at_least_6_characters_long()
    {
        $ticketCodeGenerator = new HashidsTicketCodeGenerator('salt1');

        $code = $ticketCodeGenerator->generateFor(new Ticket(['id' => 1]));

        $this->assertTrue(strlen($code) >= 6);
    }

    /** @test */
    public function ticket_codes_only_contain_uppercase_lettere()
    {
        $ticketCodeGenerator = new HashidsTicketCodeGenerator('salt1');

        $code = $ticketCodeGenerator->generateFor(new Ticket(['id' => 1]));

        $this->assertMatchesRegularExpression('/^[A-Z]+$/', $code);
    }

    /** @test */
    public function ticket_codes_for_the_same_ticket_id_are_the_same()
    {
        $ticketCodeGenerator = new HashidsTicketCodeGenerator('salt1');

        $code1 = $ticketCodeGenerator->generateFor(new Ticket(['id' => 1]));
        $code2 = $ticketCodeGenerator->generateFor(new Ticket(['id' => 1]));

        $this->assertEquals($code1, $code2);
    }

    /** @test */
    public function ticket_codes_for_diffent_ticket_ids_are_different()
    {
        $ticketCodeGenerator = new HashidsTicketCodeGenerator('salt1');

        $code1 = $ticketCodeGenerator->generateFor(new Ticket(['id' => 1]));
        $code2 = $ticketCodeGenerator->generateFor(new Ticket(['id' => 2]));

        $this->assertNotEquals($code1, $code2);
    }

    /** @test */
    public function ticket_codes_generated_with_different_salts_and_same_ticket_id_are_different()
    {
        $ticketCodeGenerator1 = new HashidsTicketCodeGenerator('salt1');
        $ticketCodeGenerator2 = new HashidsTicketCodeGenerator('salt2');

        $code1 = $ticketCodeGenerator1->generateFor(new Ticket(['id' => 1]));
        $code2 = $ticketCodeGenerator2->generateFor(new Ticket(['id' => 1]));

        $this->assertNotEquals($code1, $code2);
    }
}
