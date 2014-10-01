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
$app->get('/group/:gid', $authenticate($app), function ($gid) use ($app) {
	$group = R::load('groups', $gid);
  
	// ideally this should be done in a middleware but don't know how yet
  // need to find a better way for this
  $owner = FALSE;
  if ($_SESSION['user']['id'] == $group->owner) {
      $owner = TRUE;
  }

  $venues = R::findAll('venues', 'gid=:gid', array(':gid'=>$gid));
  // krumo($venues);

  $app->render('routes/group/group.html.twig', array(
    'page_title' => 'Group Venues',
    'groupbean' => $group,
    'venues' => $venues,
    'owner' => $owner,
  ));
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

  // need to find a better way for this
  $owner = FALSE;
  if ($_SESSION['user']['id'] == $group->owner) {
      $owner = TRUE;
  }
  else {
    $app->redirect('/user');
  }

  $venues = R::findAll('venues', 'gid=:gid', array(':gid'=>$gid));
  // krumo($venues);

  $app->render('routes/group/group.html.twig', array(
    'page_title' => 'Group Venues',
    'groupbean' => $group,
    'venues' => $venues,
  ));

});

/**
 * Add new venue
 */
$app->get('/group/:gid/venue/add', $authenticate($app), function ($gid) use ($app) {
	$group = R::load('groups', $gid);
	$owner = FALSE;
  if ($_SESSION['user']['id'] == $group->owner) {
      $owner = TRUE;
  }
  else {
    $app->redirect('/group/' . $gid);
  }

	$app->render('routes/group/group_venue_add.html.twig', array(
    'page_title' => $group->name . ' - Add Venue',
    'groupbean' => $group,
    'owner' => $owner,
  ));
});

/**
 * POST to add new venue
 */
$app->post('/group/:gid/venue/add', $authenticate($app), function ($gid) use ($app) {
	
});