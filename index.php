<?php

/// Router

require_once "vendor/autoload.php";
require_once "src/core.php";


$TG = new Telegrano(".env");


$message = explode(' ', strtolower($TG -> data['message']['text']));

// Обработчик регистрации
if(@$TG -> data['contact']['phone']) {
  $user = R::dispense("users");
  $user -> nick = $TG -> data['sender']['nick'];
  $user -> full_name = $TG -> data['sender']['first_name']." ".$TG -> data['sender']['last_name'];
  $user -> $TG -> data['chat']['id'];
  R::store($user);

  require "/src/stages/mess_welcome.php";
}

// Маршрутизатор для обработки сообщений
switch ($message[0]) {

  case '/start':
    $TG -> answerText("Assalomu aleykum, botni ishlatish uchun 'Nomerimni jonatish' tugmani bosing!", [
      [
        ['text' => 'Nomerimni Jonatish', 'request_contact' => true],
      ]
    ]);
    break;

  // case '/video':
  //   $TG -> answerVideo('https://2204.uz/bot/tgsalebot/fresco.mp4', 'Helo World!');
  //   // $TG -> answerText("Assalomu aleykum, Bu botga nomer tashiz, kodni va 24 soatni ishida 5000som pul tushadi");
  //   break;

  // case '/img':
  //   $TG -> answerPhoto('https://2204.uz/bot/tgsalebot/lenin.jpg', 'sfn fe ghf gherlgh ewuv he ervh ejv heiv heiovh erwi hdsfiluv heliuv heiug');
  //   // $TG -> answerText("Assalomu aleykum, Bu botga nomer tashiz, kodni va 24 soatni ishida 5000som pul tushadi");
  //   break;
  
  default:
    $TG -> answerText("Kechirasiz bunaqa buyruq yoq.");

    // $TG -> answerText($TG -> data['message']['text'], [], [
    //   [
    //     ['text' => '♻', 'callback_data' => 'update_this'],
    //   ],
    // ]);
    break;
}

// Маршрутизатор для вызовов встроенных кнопок
switch ($TG -> data['callback']['text']) {
  case 'update_this':
    $TG -> updateMessage($TG -> data['callback']['chat']['id'], $TG -> data['callback']['message']['id'], "🗳️🗳️🗳️TRASH🗳️🗳️🗳️", [], [
      [
        ['text' => '🗑️', 'callback_data' => 'delete_this'],
        ['text' => '🗑️', 'callback_data' => 'delete_this'],
        ['text' => '🗑️', 'callback_data' => 'delete_this']
      ],
      [
        ['text' => '🗑️', 'callback_data' => 'delete_this'],
        ['text' => '🗑️', 'callback_data' => 'delete_this'],
        ['text' => '🗑️', 'callback_data' => 'delete_this']
      ]
    ]);
    break;

  case 'delete_this':
    // Удаляем сообщение по его id
    $TG -> deleteMessage($TG -> data['callback']['chat']['id'], $TG -> data['callback']['message']['id']);
    break;
  
  default:
    # code...
    break;
}



// Шаблон функции `sendText` с клавиатурой и встроенными кнопками.

// $TG -> sendText($TG -> data['chat']['id'], $TG -> data['message']['text'], [
//   // [
//   //   ['text' => 'Button 1', 'request_contact' => true],
//   //   ['text' => 'Button 2', 'request_contact' => true]
//   // ],
//   // [
//   //   ['text' => 'Button 3', 'request_contact' => true],
//   //   ['text' => 'Button 4', 'request_contact' => true]
//   // ]
// ], [
//   // [
//   //   ['text' => 'Button 1', 'callback_data' => 'someString'],
//   //   ['text' => 'Button 2', 'callback_data' => 'someString']
//   // ],
//   // [
//   //   ['text' => 'Button 3', 'callback_data' => 'someString'],
//   //   ['text' => 'Button 4', 'callback_data' => 'someString']
//   // ]
// ]);