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
