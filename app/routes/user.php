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

  $user = R::findOne('users', 'email = :email AND password = :password', array(':email' => $email, ':password' => hash('SHA512', $password)));

  if ($user) {
    $_SESSION['user'] = array(
      'id' =>  $user->id,
      'email' => $user->email,
      'name' => $user->fname,
    );
    $app->redirect('/');
  }
  else {
    $app->render('routes/user/user_login.html.twig', array(
     'page_title' => 'User login',
      'errors' => array('Email or password incorrect.'),
    ));
  }

});

$app->get('/user/logout', function () use ($app) {
  krumo('aa');
  unset($_SESSION['user']);
  $app->view()->setData('user', null);
  krumo($_SESSION);
  // $app->redirect('/');
});

/**
 * Register new user
 */
$app->get('/user/register', function () {

});
$app->post('/user/register', function () {
  // $user = R::dispense('users');
  // $user->fname = 'Titi';
  // $user->lname = 'D';
  // $user->email = 'titi@zoocha.com';
  // $user->password = hash('SHA512', '123');
  // $id = R::store($user);
});

/**
 * add a user to a group
 * this will most probaly not be a route - just use the code inside
 * will probably do request to join/invite thing
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

/**
 * User account page
 */
$app->get('/user', $authenticate($app), function () use ($app) {
  // krumo($_SESSION);

  $uid = $_SESSION['user']['id'];

  // $user =R::findOne('users', 'id = :id', array(':id' => $uid));
  $user = R::load('users', $uid);
  // krumo($user);

  $app->render('routes/user/user_account.html.twig', array(
    'page_title' => 'User Account',
    'userbean' => $user,
  ));
});


