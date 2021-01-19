<?php


namespace App\Billing;

use Stripe\Token;
use App\Billing\Charge;
use Illuminate\Support\Arr;
use Stripe\Charge as StripeCharge;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\InvalidRequestException;

class StripePaymentGateway implements PaymentGateway
{
    const TEST_CARD_NUMBER = '4242424242424242';

    private $api_key;

    public function __construct($api_key)
    {
        $this->api_key = $api_key;
    }

    public function charge($amount, $token)
    {
        try {
            $charge = StripeCharge::create([
                'amount' => $amount,
                'currency' => 'usd',
                'source' => $token,
            ], ['api_key' => $this->api_key]);
        } catch (InvalidRequestException $e) { //todo in php8 we can remove the $e
            throw new PaymentFailedException;
        }

        return new Charge([
            'amount' => $charge->amount,
            'card_last_four' => $charge->payment_method_details->card->last4,
        ]);
    }

    public function getValidTestToken($cardNumber = self::TEST_CARD_NUMBER)
    {
        return Token::create([
            'card' => [
                'number' => $cardNumber,
                'exp_month' => 1,
                'exp_year' => date('Y') + 1,
                'cvc' => '123',
            ],
        ], ['api_key' => $this->api_key])->id;
    }

    public function newChargesDuring($callback)
    {
        $lastCharge = $this->lastCharge();

        $callback->__invoke($this);

        return $this->chargesSince($lastCharge)->map(
            fn ($charge) => new Charge(['amount' => $charge->amount])
        );
    }

    private function lastCharge()
    {
        return Arr::first(StripeCharge::all(
            ['limit' => 1],
            ['api_key' => $this->api_key]
        )['data']);
    }

    public function chargesSince($charge = null)
    {
        return collect(StripeCharge::all(
            ['ending_before' => $charge ? $charge->id : null],
            ['api_key' => $this->api_key]
        )['data']);
    }
}
