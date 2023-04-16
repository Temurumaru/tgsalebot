<?php

/// Router

require_once "vendor/autoload.php";
require_once "src/core.php";

use RedBeanPHP\R as R;


$TG = new Telegrano(".env");

try {

  $message = explode(' ', strtolower($TG -> data['message']['text']));

  $this_user_count = R::count("users", "chat_id = ?", [(@$TG -> data['chat']['id']) ? $TG -> data['chat']['id'] : $TG -> data['callback']['chat']['id']]);
  $this_user = R::findOne("users", "chat_id = ?", [(@$TG -> data['chat']['id']) ? $TG -> data['chat']['id'] : $TG -> data['callback']['chat']['id']]);

  // Обработчик регистрации
  if(@$TG -> data['contact']['phone']) {

    if($this_user_count <= 0) {
      $user = R::dispense("users");
      $user -> tag = "@".$TG -> data['sender']['nick'];
      $user -> full_name = $TG -> data['sender']['first_name']." ".$TG -> data['sender']['last_name'];
      $user -> phone = $TG -> data['contact']['phone'];
      $user -> chat_id = $TG -> data['chat']['id'];
      $user -> state = "home";
      R::store($user);

      require "src/stages/mess_welcome.php";
    } else {
      $TG -> answerText("Siz tizimda borsiz!!!");
    }
    
    

  }

  // Маршрутизатор для обработки сообщений
  switch ($message[0]) {

    case '/start':
      if($this_user_count <= 0) {
        $TG -> answerText(
          "Assalomu aleykum, botni ishlatish uchun 'Nomerimni jonatish' tugmani bosing!", 
          [
            [
              ['text' => 'Nomerimni Jonatish', 'request_contact' => true],
            ]
          ],
          [],
          true
        );
      } else {
        require "src/stages/mess_welcome.php";

      }
      break;

    default:

      if(!@$TG -> data['contact']['phone'] && !@$TG -> data['callback']['text']) {
        
        switch ($this_user -> state) {
          case 'home':
            require "src/stages/mess_welcome.php";
            break;

          case 'earn':
            require "src/stages/mess_earn.php";
            break;
          
          case 'production':
            require "src/stages/mess_production.php";
            break;

          case 'stocks':
            require "src/stages/mess_stocks.php";
            break;

          case 'about':
            require "src/stages/mess_about.php";
            break;
          
          case 'personal':
            require "src/stages/earn/mess_personal.php";
            break;

          case 'post':
            require "src/stages/earn/mess_post.php";
            break;

          case 'marketing':
            require "src/stages/earn/mess_marketing.php";
            break;
          
          default:
            $TG -> answerText("Kechirasiz bunaqa buyruq yoq.");
            break;
        }
      }

      break;
  }


  // Маршрутизатор для вызовов встроенных кнопок
  switch ($TG -> data['callback']['text']) {

    case 'earn':
      $this_user -> state = 'earn';
      R::store($this_user);
      require "src/stages/mess_earn.php";
      break;
    
    case 'production':
      $this_user -> state = 'production';
      R::store($this_user);
      require "src/stages/mess_production.php";
      break;
    
    case 'stocks':
      $this_user -> state = 'stocks';
      R::store($this_user);
      require "src/stages/mess_stocks.php";
      break;
    
    case 'about':
      $this_user -> state = 'about';
      R::store($this_user);
      require "src/stages/mess_about.php";
      break;

    case 'personal':
      $this_user -> state = 'personal';
      R::store($this_user);
      require "src/stages/earn/mess_personal.php";
      break;

    case 'post':
      $this_user -> state = 'post';
      R::store($this_user);
      require "src/stages/earn/mess_post.php";
      break;

    case 'marketing':
      $this_user -> state = 'marketing';
      R::store($this_user);
      require "src/stages/earn/mess_marketing.php";
      break;

    case 'back':
      if($this_user -> state == 'earn' || $this_user -> state == 'production' || $this_user -> state == 'stocks' || $this_user -> state == 'about') {
        $this_user -> state = 'home';
        require "src/stages/mess_welcome.php";
      }

      if($this_user -> state == 'personal' || $this_user -> state == 'post' || $this_user -> state == 'marketing') {
        $this_user -> state = 'earn';
        require "src/stages/mess_earn.php";
      }

      R::store($this_user);
      break;
    
    default:
      # code...
      break;
  }

} catch (\Throwable $th) {
  $fd = fopen("ERROR.log", 'a') or die("не удалось создать файл");
  $str = $th;
  fwrite($fd, $str);
  fclose($fd);
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