<?php
/*
Εδώ ρυθμίζουμε το 2ο Εσωτερικό Email

Η προεπιλεγμένη ρύθμιση είναι στο βασικό email.

Σημάνετε σαν σχόλιο αυτές τις ρυθμίσεις

Ενεργοποιείστε τις δικές  σας

*/

return [



  'driver' => env('MAIL_MAILER', 'smtp'),
  'host' => env('MAIL_HOST', 'smtp.mailgun.org'),
  'port' => env('MAIL_PORT', 587),
  'from' => [
      'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
      'name' => env('MAIL_FROM_NAME', 'Example'),
  ],
  'encryption' => env('MAIL_ENCRYPTION', 'tls'),
  'username' => env('MAIL_USERNAME'),
  'password' => env('MAIL_PASSWORD'),



/*
    'driver' => 'smtp',
    'host' => 'smtp.gmail.com',
    'port' =>  587,
    'from' => [
        'address' =>'ΧΧΧΧΧΧΧΧ@gmail.com',
        'name' => 'Εσωτερικό email της Υπηρεσίας',
    ],

    'encryption' => 'tls',
    'username' => 'ΧΧΧΧΧΧΧΧΧΧ',
    'password' => 'ΧΧΧΧΧΧΧΧΧΧ'
*/

];
