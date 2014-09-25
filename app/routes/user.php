<?php

/***********
* USERS
***********/

$app->get('/user/login', $isLogged($app), function () use ($app) {
  $app->render('routes/user/user_login.html.twig', array(
   'page_title' => 'User login'
  ));
});

$app->post('/user/login', function () use ($app) {
  $email = $app->request->params('email');
  $password = $app->request->params('password');

  $user = R::findOne('users', 'email = :email AND password = :password', array(':email' => $email, ':password' => hash('SHA512', $password)));

  if ($user) {
    // find which group the user belongs to 
    // (in the future it will be the default group)

    $user_group = R::findOne('user_group', 'uid = :uid', array(':uid' => $user->id));

    $gid = NULL;
    // if user belongs to a group then add the group to the session
    if (isset($user_group)) {
      $gid = $user_group->gid;      
    }

    $_SESSION['user'] = array(
      'id' =>  $user->id,
      'email' => $user->email,
      'name' => $user->fname,
      'group' => $gid,
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

$app->get('/user/logout', $authenticate($app), function () use ($app) {
  unset($_SESSION['user']);
  $app->view()->setData('user', null);
  $app->redirect('/');
});

/**
 * Register new user
 */
$app->get('/user/register', $isLogged($app), function () use ($app) {
  $flash = $app->view()->getData('flash');
  $error = isset($flash['error']) ? $flash['error'] : '';
  // krumo($error);

  $app->render('routes/user/user_register.html.twig', array(
    'page_title' => 'User Register',
    'error' => $error,
  ));
});

$app->post('/user/register', function () use ($app) {
  $fname = $app->request->params('fname');
  $lname = $app->request->params('lname');
  $email = $app->request->params('email');
  $password = $app->request->params('password');

  $check_user = R::count('users', 'email = :email', array(':email' => $email));
  
  // check if email already used
  if ($check_user > 0) {
    $app->flash('error', 'Email already registered');
    $app->redirect('/user/register');
  }
  else {
    $user = R::dispense('users');
    $user->fname = $fname;
    $user->lname = $lname;
    $user->email = $email;
    $user->password = hash('SHA512', $password);
    $user->created = time();
    $id = R::store($user);

    $_SESSION['user'] = array(
      'id' =>  $id,
      'email' => $email,
      'name' => $fname,
    );

    $app->redirect('/user');
  }

});

/**
 * Edit a user
 */
$app->get('/user/edit', $authenticate($app), function () use ($app) {
  $flash = $app->view()->getData('flash');
  $error = isset($flash['error']) ? $flash['error'] : '';
  // krumo($error);

  $uid = $_SESSION['user']['id'];
  $user = R::load('users', $uid);

  $app->render('routes/user/user_register.html.twig', array(
    'page_title' => 'User Edit',
    'error' => $error,
    'userbean' => $user,
  ));
});

$app->post('/user/edit', $authenticate($app), function () use ($app) {
  $uid = $_SESSION['user']['id'];
  $user = R::load('users', $uid);

  $fname = $app->request->params('fname');
  $lname = $app->request->params('lname');
  $email = $app->request->params('email');
  $password = $app->request->params('password');

  $user->fname = $fname;
  $user->lname = $lname;
  $user->email = $email;
  $user->password = hash('SHA512', $password);

  R::store($user);

  $app->redirect('/user');
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

  $uid = $_SESSION['user']['id'];
  if (isset($_SESSION['user']['group'])) {
    $gid = $_SESSION['user']['group']; 
    $group = R::findOne('groups', 'id = :gid', array(':gid' => $gid));
    // krumo($group);  
  }

  // $group_name = $group->name;

  // $user =R::findOne('users', 'id = :id', array(':id' => $uid));
  $user = R::load('users', $uid);
  
  // krumo($user);

  $app->render('routes/user/user_account.html.twig', array(
    'page_title' => 'User Account',
    'userbean' => $user,
    'groupbean' => isset($group) ? $group : NULL,
  ));
});
