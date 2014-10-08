<?php

/**
 * View venue details
 */
$app->get('/venue/:vid', $authenticate($app), function ($vid) use ($app) {
 	$request = $app->request();
  $resourceUri = $request->getResourceUri();
  if ($resourceUri == '/venue/add') {
    $app->pass();
  }

  $venue = R::load('venues', $vid);

  $group = R::load('groups', $venue->gid);

  // need to check as well if venue exists!
  if ($venue->id == 0) {
  	$app->redirect('/user');
  }

  // need to check if the user belongs to the group where the venue belongs to
  if ($_SESSION['user']['group'] != $venue->gid) {
  	$app->redirect('/group/' . $venue->gid);
  }


  // need to find a better way for this
  $owner = FALSE;
  if ($_SESSION['user']['id'] == $group->owner) {
      $owner = TRUE;
  }

  $app->render('routes/venue/venue.html.twig', array(
    'page_title' => $venue->name,
    'venuebean' => $venue,
    'groupbean' => $group,
    'owner' => $owner,
  ));
});

/**
 * Edit venue
 */
$app->get('/venue/:vid/edit', $authenticate($app), function ($vid) use ($app) {

	$venue = R::load('venues', $vid);

  $group = R::load('groups', $venue->gid);
  
  // krumo($group);

  // need to find a better way for this
  $owner = FALSE;
  if ($_SESSION['user']['id'] == $group->owner) {
      $owner = TRUE;
  }
  else {
  	$app->redirect('/venue/' . $vid);
  }

	$app->render('routes/venue/venue_edit.html.twig', array(
    'page_title' => $venue->name,
    'venuebean' => $venue,
    // 'owner' => ($_SESSION['user']['id'] == $group->owner) ? TRUE : FALSE,
  ));
});

/**
 * POST to edit venue
 */
$app->post('/venue/:vid/edit', $authenticate($app), function ($vid) use ($app) {
	$venue = R::load('venues', $vid);

  $venue->name = $app->request->params('vname');
  $venue->address = $app->request->params('vaddress');
  $venue->postcode = $app->request->params('vpostcode');
  $venue->town = $app->request->params('vtown');
  $venue->county = $app->request->params('vcounty');
  $venue->country = $app->request->params('vcountry');
  $venue->phone = $app->request->params('vphone');
  $venue->url = $app->request->params('vurl');

  R::store($venue);

  $app->redirect('/venue/' . $vid);

});