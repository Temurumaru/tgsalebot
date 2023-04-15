<?php

use RedBeanPHP\R as R;

global $TG;
global $message;


// if(count(R::find("users", "chat_id = ?", [(string)$TG -> data['chat']['id']])) == 0) {

//   $admin = R::dispense("users");

//   $admin -> name = $TG -> data['sender']['last_name']." ".$TG -> data['sender']['first_name'];
//   $admin -> chat_id = (string)$TG -> data['chat']['id'];
  
//   $admins = R::findAll("admin");
//   $rnd = array_rand($admins);

//   $admin -> admin_id = (string)$admins[$rnd] -> id;

//   R::store($admin);

//   $TG -> answerText("Assalomu aleykum <b>".$TG -> data['sender']['last_name']." ".$TG -> data['sender']['first_name']."</b>!\nSiz admin Boldingiz!");

// } else {
//   $TG -> answerText("Siz adminsiz token kerakmas!");
// }