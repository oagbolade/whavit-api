<?php

function paystack_public_key()
{
    $key = env('PAYSTACK_PUBLIC_KEY');
    return $key;
}

function paystack_secret_key()
{
    $key = env('PAYSTACK_SECRET_KEY','sk_test_f03d1dce99f10c308c5a8492d4dcdd80458e9d2d');
    return $key;
}
