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
  
  krumo($group);

  // need to find a better way for this
  $owner = FALSE;
  if ($_SESSION['user']['id'] != $group->owner) {
      $owner = TRUE;
  }

  $app->render('routes/venue/venue.html.twig', array(
    'page_title' => $venue->name,
    'venuebean' => $venue,
    'owner' => $owner,
  ));
});

/**
 * Edit venue
 */
$app->get('/venue/:vid/edit', $authenticate($app), function ($vid) use ($app) {
	$app->render('routes/venue/venue_edit.html.twig', array(
    'page_title' => '$venue->name',
    // 'venuebean' => $venue,
    // 'owner' => ($_SESSION['user']['id'] == $group->owner) ? TRUE : FALSE,
  ));
});

/**
 * POST to edit venue
 */
$app->post('/venue/:vid/edit', $authenticate($app), function ($vid) use ($app) {

});

/**
 * Add new venue
 */
$app->get('/venue/add', $authenticate($app), function () use ($app) {

});

/**
 * POST to add new venue
 */
$app->post('/venue/add', $authenticate($app), function ($vid) use ($app) {

});