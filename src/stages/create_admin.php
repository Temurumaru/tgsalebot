<?php

use RedBeanPHP\R as R;

global $TG;
global $message;


if(count(R::find("admins", "chat_id = ?", [(string)$TG -> data['chat']['id']])) == 0) {

  $admin = R::dispense("admins");

  $admin -> name = $TG -> data['sender']['last_name']." ".$TG -> data['sender']['first_name'];
  $admin -> chat_id = (string)$TG -> data['chat']['id'];
  $admin -> premission = "admin";

  R::store($admin);

  $TG -> answerText("Assalomu aleykum <b>".$TG -> data['sender']['last_name']." ".$TG -> data['sender']['first_name']."</b>!\nSiz admin Boldingiz!");

} else {
  $TG -> answerText("Siz adminsiz token kerakmas!");
}