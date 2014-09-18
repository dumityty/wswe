<?php

/***********
* USERS
***********/


$app->get('/user/login', function () use ($app) {
  $app->render('routes/user/user_login.html.twig', array(
   'page_title' => 'User login'
  ));
});

$app->post('/user/login', function () use ($app) {
  $email = $app->request->params('email');
  $password = $app->request->params('password');

  krumo($email);
  krumo($password);

  $user = R::findOne('users', 'email = :email AND password = :password', array(':email' => $email, ':password' => hash('SHA512', $password)));

  krumo($user);

  if ($user) {
    $app->redirect('/');
    krumo('success');
  } else {
    krumo('fail');

    $app->render('routes/user/user_login.html.twig', array(
     'page_title' => 'User login',
        'errors' => array('Email or password incorrect.'),
    ));
  }

});

/**
 * this will be a post to save a new user
 */
$app->get('/post', function () {
  $user = R::dispense('users');
  $user->fname = 'Titi';
  $user->lname = 'D';
  $user->email = 'titi@zoocha.com';
  $user->password = hash('SHA512', '123');
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
