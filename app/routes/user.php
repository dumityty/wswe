<?php

/***********
* USERS
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

/**
 * add a user to a group
 */
$app->get('/user-group/:uid/:gid', function ($uid, $gid) {
  // $user_group = R::dispense('usergroup');
  // $user_group->uid = '1';
  // $user_group->gid = '1';
  // $id = R::store($user_group);
  $sql = "INSERT INTO user_group (uid,gid) VALUES (:uid, :gid)";
  R::exec($sql, array(':uid' => $uid, ':gid' => $gid));
  echo "Done.";
});

/***********
* GROUPS
***********/

/**
 * this will be a post to save a new group
 */
$app->get('/group', function () {
  $group = R::dispense('groups');
  $group->name = 'Group1';
  $group->owner = 1;
  $id = R::store($group);
});
