<?php
return [
    'status'        => env('SMS_STATUS', 'inactive'),
    'driver'        => env('SMS_DRIVER', 'kavenegar'),
    'manager_phone' => env('SMS_MANAGER_PHONE', ''),
    'kavenegar' => [
        'api_key'     => env('SMS_KAVENEGAR_API_KEY', ''),
        'line_number' => env('SMS_KAVENEGAR_LINE_NUMBER', ''),
    ],
];
