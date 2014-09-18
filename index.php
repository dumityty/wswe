<?php

require 'vendor/autoload.php';
require 'vendor/rb.php';


// Include the app configuration file.
// require_once dirname(dirname(__FILE__)) . '/app/config.php';

R::setup('mysql:host=localhost;
        dbname=wswe','root','titi');

$app = new \Slim\Slim(array(
    'templates.path' => 'templates',
));

$app->add(new \Slim\Middleware\SessionCookie(array(
    'secure' => false,
    'name' => 'wswe_session',
    'secret' => 'd987jdskjh8293kjhcs3289',
    'cipher' => MCRYPT_RIJNDAEL_256,
    'cipher_mode' => MCRYPT_MODE_CBC,
)));

$app->view(new \Slim\Views\Twig());
$app->view->parserOptions = array(
    'charset' => 'utf-8',
    // 'cache' => realpath('../templates/cache'),
    'auto_reload' => true,
    'strict_variables' => false,
    'autoescape' => true
);
$app->view->parserExtensions = array(new \Slim\Views\TwigExtension());


// MIDDLEWARES
// require '../app/middleware/authenticate.php';
require 'app/middleware/user.php';

// ROUTES
// require '../app/routes/user.php';
require 'app/routes/user.php';


// brain of the app
// gets a list of venues
// gets all of today's votes
// gets votes/venue
//
// @todo
// passes all info to the twig template
// to show a list of venues and the vote data
$app->get('/', $authenticate($app), function () use ($app) {

    $venues = R::findAll('venues');
    $venue_full = array();
    $total_votes = 0;

    $votes = R::find('votes', 'DATE(FROM_UNIXTIME(`time`)) = CURDATE()');

    // krumo($votes);
    // krumo($venues);

    // if (count($votes)) {
	    foreach ($venues as $key => $venue) {
    		$venue_votes_query = R::find('votes', 'DATE(FROM_UNIXTIME(`time`)) = CURDATE() AND vid LIKE :vid', array( ':vid' => $venue['id'] ));
    		$venue_votes = count($venue_votes_query);

    		// krumo($venue_votes_query);
    		
    		$venue_full[$key]['id'] = $venue['id'];
	    	$venue_full[$key]['name'] = $venue['name'];
	    	$venue_full[$key]['votes'] = $venue_votes;
	    	$total_votes += $venue_votes;
	    }
    // }
    // else {

    // }

    $app->render('routes/index.html.twig', array(
    	'page_title' => 'WSWE',
    	'venues' => $venue_full,
    	'total_votes' => $total_votes,
  	));
});

// this will be a post to add new venues
// need an actual get to get some info on the venue
$app->post('/group/venue', $authenticate($app), function() {
	// $venue = R::dispense('venues');
	// $venue->name = 'V2';
	// $venue->address = '12 abc';
	// $venue->postcode = 'sg141px';
	// $venue->phone = '12345678';
	// $venue->email = 'abc@bla.com';
	// $id = R::store($venue);
});

// this will be a post to add a user's vote
// also will need a get maybe to get a user's vote?
$app->get('/vote/:uid/:vid', $authenticate($app), function($uid, $vid) use ($app) {
	// check if user already voted today
    $user_voted =R::count('votes', 'DATE(FROM_UNIXTIME(`time`)) = CURDATE() AND uid = :uid', array( ':uid' => $uid ));

    // if user hasn't voted, then just add the new vote
    if ($user_voted == 0) {
    	$vote = R::dispense('votes');
    	$vote->vid = $vid;
    	$vote->uid = $uid;
    	$vote->time = time();
    	$id = R::store($vote);
      // echo "Voted!";
    	$app->redirect('/');
    }
    // need to update the vote with the new venue
    else {
      // echo "User already voted!";
      // echo "<br />";

      $user_vote =R::findOne('votes', 'DATE(FROM_UNIXTIME(`time`)) = CURDATE() AND uid = :uid', array( ':uid' => $uid ));
      $user_vote->vid = $vid;
      $user_vote->time = time();
      R::store($user_vote);

      // echo "Vote changed!";
      // echo "<br />";
      $app->redirect('/');
    }
});


$app->run();



/**
 * Sets the response type, status code and the body.
 * Function used in all route files to return the JSON response.
 * @param int $status_code [description]
 * @param Object $body        [description]
 */
// function setResponse($status_code, $body) {
//   $app = \Slim\Slim::getInstance();
//   $response = $app->response();
//   $response['Content-Type'] = 'application/json';
//   $response['X-Powered-By'] = 'Mydex';
//   $response->status($status_code);
//   $response->body(json_encode($body));
// }
