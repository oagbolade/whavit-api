<?php

namespace App\Http\Controllers\API\Main;

use App\Http\Controllers\Controller;

use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Classes\InvoiceItem;

class DisinfectionInvoice extends Controller
{
    public function show($name, $company_name, $message, $quantity, $amount)
    {
        $client = new Party([
            'name'          => 'Whavit Technologies Limited',
            'address'         => '9a Abagbon Close, Victoria Island, Lagos State.',
            'phone'         => '09092434742',
            'custom_fields' => [
                'website'        => 'www.whavit.com',
            ],
        ]);



        $customer = new Party([
            'name'          => $name,
            'custom_fields' => [
                'company name' => $company_name,
                'description' => $message,
            ],
        ]);

        $items = [
            (new InvoiceItem())->title('Disinfection')->pricePerUnit(350)->quantity($quantity)->discount(0)->units('N/A'),
        ];

        $notes = [
            'Kindly make the full payment within 15-30 days',
            'Whavit Technologies',
            'GTBank',
            '0524132381',
            'There is a 7.5% VAT of the total amount',
        ];
        $notes = implode("<br>", $notes);

        $six_digit_random_number = mt_rand(100000, 999999);

        $invoice = Invoice::make('receipt')
            ->series('BIG')
            ->sequence($six_digit_random_number)
            ->serialNumberFormat('{SEQUENCE}/{SERIES}')
            ->seller($client)
            ->buyer($customer)
            ->date(now())
            ->dateFormat('d/m/Y')
            ->payUntilDays(15)
            ->currencySymbol('₦')
            ->currencyCode('NGN')
            ->currencyFormat('{SYMBOL}{VALUE}')
            ->currencyThousandsSeparator(',')
            ->currencyDecimalPoint('.')
            ->filename($customer->name .' invoice')
            ->addItems($items)
            ->notes($notes)
            ->logo(public_path('vendor/invoices/whavit-logo.png'));
            
        $link = $invoice->url();
        // Then send email to party with link

        // And return invoice itself to browser or have a different view
        return $invoice->download();
    }
}
