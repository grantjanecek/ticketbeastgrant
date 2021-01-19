<?php


namespace App\Billing;

use App\Billing\Charge;
use Illuminate\Support\Str;

class FakePaymentGateway implements PaymentGateway
{
    const TEST_CARD_NUMBER = '4242424242424242';
    private $charges;
    private $tokens;
    private $beforeFirstChargeCallback;

    public function __construct()
    {
        $this->charges = collect();
        $this->tokens = collect();
    }

    public function getValidTestToken($cardNumber = self::TEST_CARD_NUMBER)
    {
        $token = 'fake_tok'.Str::random(24);

        $this->tokens->put($token, $cardNumber);

        return $token;
    }

    public function charge($amount, $token)
    {
        if ($this->beforeFirstChargeCallback !== null) {
            $callback = $this->beforeFirstChargeCallback;
            $this->beforeFirstChargeCallback = null;
            $callback($this);
        }

        if (! $this->tokens->has($token)) {
            throw new PaymentFailedException();
        }

        return $this->charges[] = new Charge([
            'amount' => $amount,
            'card_last_four' => Str::substr($this->tokens->get($token), -4),
        ]);
    }

    public function totalCharges()
    {
        return $this->charges->map->amount()->sum();
    }

    public function beforeFirstCharge($calback)
    {
        $this->beforeFirstChargeCallback = $calback;
    }

    public function newChargesDuring($callback)
    {
        $fromCharges = $this->charges->count();

        $callback->__invoke($this);

        return $this->charges->slice($fromCharges)->reverse()->values();
    }
}
