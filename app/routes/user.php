<?php

/***********
* GET
***********/

/**
 * this will be a post to save a new user
 */
$app->get('/user', function () {
  $user = R::dispense('users');
  $user->fname = 'Titi';
  $user->lname = 'D';
  $user->email = 'titi@zoocha.com';
  $id = R::store($user);
});
