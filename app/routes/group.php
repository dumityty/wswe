<?php

/***********
* GROUPS
***********/

/**
 * GET a list of all groups ??
 * shouldn't be a route by itself
 * more likely a function to get a user's groups
 */

$app->get('/groups', function () {

});


/**
 * GET one group
 * Name, Venues belonging to group
 * maybe the people belonging to group if they are owner
 */
$app->get('/group', function () {

});

/**
 * POST to create new group
 */
$app->post('/group', function () {
  $group = R::dispense('groups');
  $group->name = 'Group1';
  $group->owner = 1;
  $id = R::store($group);
});

/**
 * User join group form
 */
$app->get('/group/join', $authenticate($app), function () use ($app) {
  $app->render('routes/user/user_join_group.html.twig', array(
    'page_title' => 'User Join Group',
  ));
});

$app->post('/group/join', $authenticate($app), function () use ($app) {
  $uid = $_SESSION['user']['id'];

  $gid = $app->request->params('group');

  $sql = "INSERT INTO user_group (uid,gid) VALUES (:uid, :gid)";
  R::exec($sql, array(':uid' => $uid, ':gid' => $gid));

  $_SESSION['user']['group'] = $gid;

  $app->redirect('/user');
});

/**
 * GET to leave the group
 * 
 * NOTE: this should probably be a POST?
 */
$app->get('/group/:gid/leave', $authenticate($app), function ($gid) use ($app) {
	$uid = $_SESSION['user']['id'];
	$sql = "DELETE FROM user_group WHERE uid = :uid AND gid = :gid";
  R::exec($sql, array(':uid' => $uid, ':gid' => $gid));

  // krumo($_SESSION);
  unset($_SESSION['user']['group']);
  
  $app->redirect('/user');
});

$app->get('/group/:gid/manage', $authenticate($app), function ($gid) use ($app) {
	// ideally this should be done in a middleware but don't know how yet
	$group = R::load('groups', $gid);
  if ($_SESSION['user']['id'] != $group->owner) {
      $app->redirect('/user');
  }

  $venues = R::findAll('venues', 'gid=:gid', array(':gid'=>$gid));
  krumo($venues);

  $app->render('routes/group/venues.html.twig', array(
    'page_title' => 'Group Venues',
    'group_name' => $group->name,
    'venues' => $venues,
  ));

});

