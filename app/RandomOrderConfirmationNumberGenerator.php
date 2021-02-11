<?php

namespace App;

class RandomOrderConfirmationNumberGenerator implements OrderConfirmationNumberGenerator, InvitationCodeGenerator
{
    public function generate()
    {
        return substr(str_shuffle(str_repeat('23456789ABCDEWFGHJKLMNPQRSTUVWXYZ', 24)), 0, 24);
    }
}
