<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Exception;

class PaymentGateway
{
  public static function charge(string $paymentMethodId, float $amount): object
  {
    Stripe::setApiKey(config('services.stripe.secret'));

    try {
      $paymentIntent = PaymentIntent::create([
        'amount' => intval($amount * 100),
        'currency' => config('services.stripe.currency', 'gbp'),
        'payment_method' => $paymentMethodId,
        'confirm' => true,
        'automatic_payment_methods' => [
          'enabled' => true,
          'allow_redirects' => 'never',
        ],
      ]);

      return (object) [
        'success' => true,
        'transaction_id' => $paymentIntent->id,
        'status' => $paymentIntent->status,
      ];
    } catch (Exception $e) {
      return (object) [
        'success' => false,
        'error' => $e->getMessage(),
      ];
    }
  }
}
