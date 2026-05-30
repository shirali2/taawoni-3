<?php return array (
  'beyondcode/laravel-dump-server' => 
  array (
    'providers' => 
    array (
      0 => 'BeyondCode\\DumpServer\\DumpServerServiceProvider',
    ),
  ),
  'fideloper/proxy' => 
  array (
    'providers' => 
    array (
      0 => 'Fideloper\\Proxy\\TrustedProxyServiceProvider',
    ),
  ),
  'hekmatinasser/verta' => 
  array (
    'providers' => 
    array (
      0 => 'Hekmatinasser\\Verta\\Laravel\\VertaServiceProvider',
    ),
    'aliases' => 
    array (
      'Verta' => 'Hekmatinasser\\Verta\\Verta',
    ),
  ),
  'kavenegar/laravel' => 
  array (
    'providers' => 
    array (
      0 => 'Kavenegar\\Laravel\\ServiceProvider',
    ),
    'aliases' => 
    array (
      'Kavenegar' => 'Kavenegar\\Laravel\\Facade',
    ),
  ),
  'laravel/nexmo-notification-channel' => 
  array (
    'providers' => 
    array (
      0 => 'Illuminate\\Notifications\\NexmoChannelServiceProvider',
    ),
  ),
  'laravel/slack-notification-channel' => 
  array (
    'providers' => 
    array (
      0 => 'Illuminate\\Notifications\\SlackChannelServiceProvider',
    ),
  ),
  'laravel/tinker' => 
  array (
    'providers' => 
    array (
      0 => 'Laravel\\Tinker\\TinkerServiceProvider',
    ),
  ),
  'maatwebsite/excel' => 
  array (
    'providers' => 
    array (
      0 => 'Maatwebsite\\Excel\\ExcelServiceProvider',
    ),
    'aliases' => 
    array (
      'Excel' => 'Maatwebsite\\Excel\\Facades\\Excel',
    ),
  ),
  'nesbot/carbon' => 
  array (
    'providers' => 
    array (
      0 => 'Carbon\\Laravel\\ServiceProvider',
    ),
  ),
  'nunomaduro/collision' => 
  array (
    'providers' => 
    array (
      0 => 'NunoMaduro\\Collision\\Adapters\\Laravel\\CollisionServiceProvider',
    ),
  ),
  'shetabit/payment' => 
  array (
    'providers' => 
    array (
      0 => 'Shetabit\\Payment\\Provider\\PaymentServiceProvider',
    ),
    'aliases' => 
    array (
      'Payment' => 'Shetabit\\Payment\\Facade\\Payment',
    ),
  ),
);