<?php

/**
 * # Telegrano Core
 * @name Telegrano Core
 * @file core.php
 * @author Temurumaru <temurumaru@yandex.com>
 * @link https://www.2204.uz
 * 
 * @depends gabordemooij/redbean (ORM)
 * 
 * # Bot webhook fix link
 * @link https://api.telegram.org/bot[YOUR BOT TOKEN]/setWebhook?url=[YOUR DOMEN BOT PATH]
 */


require "vendor/autoload.php";

use RedBeanPHP\R as R;
use RedBeanPHP\Util\DispenseHelper as DH;


/**
 * @name Telegrano
 */
class Telegrano {
  
  /**
   * Environment settings array
   * 
   * @var array
   */
  public array $env;

  /**
   * Array of Telegram requests
   * 
   * @var array
   */
  public array $data;

  /**
   * Usage: 
   * 
   * ```
   * new Telegrano(".env");
   * ```
   * @param string $env_file  Environment settings file relative path
   */
  function __construct($env_file) {

    $fh = fopen($env_file, 'r');
    while (!feof($fh)) {
      $line = fgets($fh);
      if(trim($line)) $this -> env[trim(explode('=', $line)[0])] = trim(explode('=', $line)[1]);
    }
    fclose($fh);


    R::setup($this -> env['DB_CONNECTION'].':host='.$this -> env['DB_HOST'].';dbname='.$this -> env['DB_DATABASE'], $this -> env['DB_USERNAME'], $this -> env['DB_PASSWORD']);
    DH::setEnforceNamingPolicy(false);

    if(!R::testConnection()) {
      exit("There is no connection to the database :(");
    }

    $mess = file_get_contents('php://input');
    $mess = json_decode($mess, true);

    // $fd = fopen("rst.txt", 'a') or die("не удалось создать файл");
    // $str = json_encode(json_encode($mess));
    // fwrite($fd, $str);
    // fclose($fd);

    $this -> data['sender']['id'] = $mess['message']['from']['id'];
    $this -> data['sender']['nick'] = $mess['message']['from']['username'];
    $this -> data['sender']['first_name'] = $mess['message']['from']['first_name'];
    $this -> data['sender']['last_name'] = $mess['message']['from']['last_name'];
    $this -> data['chat']['id'] = $mess['message']['chat']['id'];
    $this -> data['message']['id'] = $mess['message']['message_id'];
    $this -> data['message']['text'] = $mess['message']['text'];
    $this -> data['message']['photo'] = $mess['message']['message']['photo'];

    $this -> data['entities']['type'] = $mess['message']['entities']['type'];
    $this -> data['contact']['phone'] = $mess['message']['contact']['phone_number'];

    $this -> data['callback']['message']['id'] = $mess['callback_query']['message']['message_id'];
    $this -> data['callback']['chat']['id'] = $mess['callback_query']['message']['chat']['id'];
    $this -> data['callback']['text'] = $mess['callback_query']['data'];


    unset($mess);
  }

  
  public function sendText($chat_id, $text, $panel_keyboard = [], $inline_keyboard = [], $one_time = false, $resizeble = true) {

    $tg_server="https://api.telegram.org/bot".$this -> env['APP_TG_TOKEN'];

    $keyboard = [
      "one_time_keyboard" => $one_time,
      "resize_keyboard" => $resizeble
    ];

    if($panel_keyboard) {
      $keyboard['keyboard'] = $panel_keyboard;
    } else if($inline_keyboard) {
      $keyboard['inline_keyboard'] = $inline_keyboard;
    }
      
    // array_push($keyboard['keyboard'][0], ['text' => 'Войти', 'request_contact' => true]);
    

    // $fd = fopen("rst.txt", 'a') or die("не удалось создать файл");
    // $str = json_encode(json_encode($this -> data['callback']));
    // fwrite($fd, $str);
    // fclose($fd);

    $params=[
      'chat_id' => $chat_id,
      'parse_mode' => 'html',
      'text' => (string)$text,
      'reply_markup' => json_encode($keyboard),
    ];

    $ch = curl_init($tg_server . '/sendMessage');
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    
    curl_close($ch);

    return $result;

  }

  public function answerText($text, $panel_keyboard = [], $inline_keyboard = [], $one_time = false, $resizeble = true) {
    return $this -> sendText($this -> data['chat']['id'], $text, $panel_keyboard, $inline_keyboard, $one_time, $resizeble);
  }

  public function sendVideo(int|string $chat_id, string $video, string $text = "", array $panel_keyboard = [], array $inline_keyboard = [], bool $one_time = false, bool $resizeble = true) {

    $tg_server="https://api.telegram.org/bot".$this -> env['APP_TG_TOKEN'];

    // $fd = fopen("rst.txt", 'a') or die("не удалось создать файл");
    // $str = json_encode(json_encode($this -> data['callback']));
    // fwrite($fd, $str);
    // fclose($fd);

    $keyboard = [
      "one_time_keyboard" => $one_time,
      "resize_keyboard" => $resizeble
    ];

    if($panel_keyboard) {
      $keyboard['keyboard'] = $panel_keyboard;
    } else if($inline_keyboard) {
      $keyboard['inline_keyboard'] = $inline_keyboard;
    }
      
    // array_push($keyboard['keyboard'][0], ['text' => 'Войти', 'request_contact' => true]);
    

    // $fd = fopen("rst.txt", 'a') or die("не удалось создать файл");
    // $str = json_encode(json_encode($this -> data['callback']));
    // fwrite($fd, $str);
    // fclose($fd);

    $params=[
      'chat_id' => $chat_id,
      'parse_mode' => 'html',
      'caption' => $text,
      'reply_markup' => json_encode($keyboard),
      'video' => $video,
    ];

    $ch = curl_init($tg_server . '/sendVideo');
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);

    // $fd = fopen("rst.txt", 'a') or die("не удалось создать файл");
    // $str = json_encode($result);
    // fwrite($fd, $str);
    // fclose($fd);
    // curl_close($ch);

    return $result;

  }

  public function answerVideo(string $video, string $text = "", array $panel_keyboard = [], array $inline_keyboard = [], bool $one_time = false, bool $resizeble = true) {
    return $this -> sendVideo($this -> data['chat']['id'], $video, $text, $panel_keyboard, $inline_keyboard, $one_time, $resizeble);
  }

  public function sendPhoto(int|string $chat_id, string $photo, string $text = "", array $panel_keyboard = [], array $inline_keyboard = [], bool $one_time = false, bool $resizeble = true) {

    $tg_server="https://api.telegram.org/bot".$this -> env['APP_TG_TOKEN'];

    $keyboard = [
      "one_time_keyboard" => $one_time,
      "resize_keyboard" => $resizeble
    ];

    if($panel_keyboard) {
      $keyboard['keyboard'] = $panel_keyboard;
    } else if($inline_keyboard) {
      $keyboard['inline_keyboard'] = $inline_keyboard;
    }
      
    // array_push($keyboard['keyboard'][0], ['text' => 'Войти', 'request_contact' => true]);
    

    // $fd = fopen("rst.txt", 'a') or die("не удалось создать файл");
    // $str = json_encode(json_encode($this -> data['callback']));
    // fwrite($fd, $str);
    // fclose($fd);

    $params=[
      'chat_id' => $chat_id,
      'parse_mode' => 'html',
      'caption' => $text,
      'reply_markup' => json_encode($keyboard),
      'photo' => $photo,
    ];

    $ch = curl_init($tg_server . '/sendPhoto');
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    
    curl_close($ch);

    return $result;

  }

  public function answerPhoto(string $photo, string $text = "", array $panel_keyboard = [], array $inline_keyboard = [], bool $one_time = false, bool $resizeble = true) {
    return $this -> sendPhoto($this -> data['chat']['id'], $photo, $text, $panel_keyboard, $inline_keyboard, $one_time, $resizeble);
  }

  public function updateMessage($chat_id, $id, $text, $panel_keyboard = [], $inline_keyboard = [], $one_time = false, $resizeble = true) {

    $tg_server="https://api.telegram.org/bot".$this -> env['APP_TG_TOKEN'];

    $keyboard = [
      "one_time_keyboard" => $one_time,
      "resize_keyboard" => $resizeble
    ];

    if($panel_keyboard) {
      $keyboard['keyboard'] = $panel_keyboard;
    } else if($inline_keyboard) {
      $keyboard['inline_keyboard'] = $inline_keyboard;
    }
      
    // array_push($keyboard['keyboard'][0], ['text' => 'Войти', 'request_contact' => true]);
    

    // $fd = fopen("rst.txt", 'a') or die("не удалось создать файл");
    // $str = json_encode($this -> data['callback']);
    // fwrite($fd, $str);
    // fclose($fd);

    $params=[
      'chat_id' => $chat_id,
      'message_id' => (string)$id,
      'parse_mode' => 'html',
      'text'=> (string)$text,
      'reply_markup' => json_encode($keyboard),
    ];

    $ch = curl_init($tg_server . '/editMessageText');
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    
    curl_close($ch);

    return $result;

  }

  public function deleteMessage($chat_id, $id) {

    $tg_server="https://api.telegram.org/bot".$this -> env['APP_TG_TOKEN'];

    $params = [
      'chat_id' => $chat_id,
      'message_id' => (string)$id
    ];

    $ch = curl_init($tg_server . '/deleteMessage');
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    
    curl_close($ch);

    return $result;

  }

}