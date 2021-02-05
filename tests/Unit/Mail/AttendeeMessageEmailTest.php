<?php

namespace Tests\Unit\Mail;

use App\Mail\AttendeeMessageEmail;
use App\Mail\OrderConfirmationEmail;
use App\Models\AttendeeMessage;
use App\Models\Order;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttendeeMessageEmailTest extends TestCase
{
    /** @test */
    function email_has_the_correct_subject_and_message()
    {
        $message = new AttendeeMessage([
            'subject' => 'My Subject',
            'message' => 'My Message',
        ]);

        $email = new AttendeeMessageEmail($message);

        $this->assertEquals('My Subject', $email->build()->subject);
        $this->assertEquals('My Message', trim($email->render()));
    }
}
