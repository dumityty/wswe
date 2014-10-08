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
	$request = $app->request();
  $resourceUri = $request->getResourceUri();
  if ($resourceUri == '/group/join') {
    $app->pass();
  }

	$group = R::load('groups', $gid);

	// need to check if group exists
	if ($group->id == 0) {
		$app->redirect('/user');
	}

	// need to check if user belongs to group
	// otherwise not allowed to view group
	// for now check for session group as we only allow one group
	// in the future, will need to check for the user_group table
	if ($_SESSION['user']['group'] != $gid) {
		$app->redirect('/user');		
	}
  
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


// this whole route will probably be changed to Invitation system.
// for now, maybe restrict user to just be able to join one group at a time.
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
	// check maybe if the venue exists already in that group(!)
	// doesn't matter if it exists in another group - they are independent

	$venue = R::dispense('venues');

	$venue->name = $app->request->params('vname');
  $venue->address = $app->request->params('vaddress');
  $venue->postcode = $app->request->params('vpostcode');
  $venue->town = $app->request->params('vtown');
  $venue->county = $app->request->params('vcounty');
  $venue->country = $app->request->params('vcountry');
  $venue->phone = $app->request->params('vphone');
  $venue->url = $app->request->params('vurl');
  $venue->gid = $gid;


	$id = R::store($venue);

  $app->redirect('/group/' . $gid);
});

/**
 * Edit group
 */
$app->get('/group/:gid/edit', $authenticate($app), function ($gid) use ($app) {

	$group = R::load('groups', $gid);

	$owner = FALSE;
  if ($_SESSION['user']['id'] == $group->owner) {
      $owner = TRUE;
  }
  else {
  	$app->redirect('/user');
  }

	$app->render('routes/group/group_edit.html.twig', array(
    'page_title' => $group->name,
    'groupbean' => $group,
    // 'owner' => ($_SESSION['user']['id'] == $group->owner) ? TRUE : FALSE,
  ));

});

/**
 * Edit group
 */
$app->post('/group/:gid/edit', $authenticate($app), function ($gid) use ($app) {

	$group = R::load('groups', $gid);
	$group->name = $app->request->params('gname');

  R::store($group);

  $app->redirect('/group/' . $gid);

});