<?php

use RedBeanPHP\R as R;

global $TG;
global $message;

$TG -> answerPhoto(
  'image.jpg',
  "<b>Akciya #1</b> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s."
);

$TG -> answerPhoto(
  'image.jpg',
  "<b>Akciya #2</b> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s."
);

$TG -> answerPhoto(
  'image.jpg',
  "<b>Akciya #3</b> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.",
  [],
  [
    [
      ['text' => 'Orqaga ↩️', 'callback_data' => 'back'],
    ]
  ],
  true
);