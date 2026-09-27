<?php

use Domain\Payments\Gateways\PagBankGateway;

it('validates the PagBank webhook authenticity token using the raw payload', function () {
    $gateway = new PagBankGateway;
    $gateway->configure([
        'token' => 'sandbox-token',
        'sandbox' => true,
    ]);

    $payload = '{"id":"CHAR_TEST","status":"PAID"}';
    $signature = hash('sha256', 'sandbox-token-' . $payload);

    expect($gateway->validateWebhookSignature(
        ['x-authenticity-token' => [$signature]],
        $payload
    ))->toBeTrue();
});

it('rejects an invalid PagBank webhook authenticity token', function () {
    $gateway = new PagBankGateway;
    $gateway->configure([
        'token' => 'sandbox-token',
        'sandbox' => true,
    ]);

    expect($gateway->validateWebhookSignature(
        ['x-authenticity-token' => ['invalid']],
        '{"id":"CHAR_TEST","status":"PAID"}'
    ))->toBeFalse();
});

it('rejects a PagBank webhook without an authenticity token', function () {
    $gateway = new PagBankGateway;
    $gateway->configure([
        'token' => 'sandbox-token',
        'sandbox' => true,
    ]);

    expect($gateway->validateWebhookSignature(
        [],
        '{"id":"CHAR_TEST","status":"PAID"}'
    ))->toBeFalse();
});
