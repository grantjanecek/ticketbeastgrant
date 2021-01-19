<?php

namespace App\Http\Controllers;

use App\Reservation;
use App\Models\Order;
use App\Models\Concert;
use Illuminate\Http\Request;
use App\Billing\PaymentGateway;
use App\Mail\OrderConfirmationEmail;
use Illuminate\Support\Facades\Mail;
use App\Billing\PaymentFailedException;
use Illuminate\Support\Facades\Response;
use App\Exceptions\NotEnoughTicketsException;

class ConcertOrdersController extends Controller
{
    private $paymentGateway;

    public function __construct(PaymentGateway $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    public function store($concertId)
    {
        $concert = Concert::published()->findOrFail($concertId);

        $this->validate(\request(), [
            'email' => ['required', 'email'],
            'ticket_quantity' => ['required', 'numeric' ,'min:1'],
            'payment_token' => ['required']
        ]);

        try {
            $reservation = $concert->reserveTickets(\request('ticket_quantity'), \request('email'));
            $order = $reservation->complete($this->paymentGateway, \request('payment_token'));

            Mail::to($order->email)->send(new OrderConfirmationEmail($order));

            return Response::json($order, 201);
        } catch (PaymentFailedException $e) {
            $reservation->cancel();
            return Response::json([],422);
        } catch (NotEnoughTicketsException $e) {
            return Response::json([],422);
        }
    }
}
