<?php

require 'vendor/autoload.php';
require 'vendor/rb.php';

// Include the app configuration file.
// require_once dirname(dirname(__FILE__)) . '/app/config.php';

R::setup('mysql:host=localhost;
        dbname=wswe','root','zoocha');

$app = new \Slim\Slim(array(
    'templates.path' => '../templates',
));

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
$app->get('/', function () {
    echo "WSWE.TODAY";
    echo "<br>";
    echo "<br>";
    // $votes = R::findAll('vote');
    $venues = R::findAll('venues');

    $votes = R::find('votes', 'DATE(FROM_UNIXTIME(`time`)) = CURDATE()');

    if (count($votes)) {
    	echo 'There are votes';
    	echo "<br>";

	    foreach ($venues as $key => $venue) {
    		$venue_votes_query = R::find('votes', 'DATE(FROM_UNIXTIME(`time`)) = CURDATE() AND venue LIKE :venue', array( ':venue' => $venue['id'] ));
    		$venue_votes = count($venue_votes_query);
    		echo $venue['name'] . ' - '. $venue_votes;
	    	echo "<br>";
	    }
    }
    else {
    	echo 'No votes today!';
	    echo "<br>";
    }

    echo "<br>";
    echo "Done.";
});

// this will be a post to add new venues
// need an actual get to get some info on the venue
$app->get('/venue', function() {
	$venue = R::dispense('venues');
	$venue->name = 'V2';
	$venue->address = '12 abc';
	$venue->postcode = 'sg141px';
	$venue->phone = '12345678';
	$venue->email = 'abc@bla.com';
	$id = R::store($venue);
});

// this will be a post to add a user's vote
// also will need a get maybe to get a user's vote?
$app->get('/vote/:user/:venue_id', function($user, $venue_id) {
	// check if user already voted today
    $user_voted =R::count('votes', 'DATE(FROM_UNIXTIME(`time`)) = CURDATE() AND user = :user', array( ':user' => $user ));

    // if user hasn't voted, then just add the new vote
    if ($user_voted == 0) {
    	$vote = R::dispense('votes');
    	$vote->venue = $venue_id;
    	$vote->user = $user;
    	$vote->time = time();
    	$id = R::store($vote);
        echo "Voted!";
    }
    // need to update the vote with the new venue
    else {
        echo "User already voted!";
        echo "<br />";

        $user_vote =R::findOne('votes', 'DATE(FROM_UNIXTIME(`time`)) = CURDATE() AND user = :user', array( ':user' => $user ));
        $user_vote->venue = $venue_id;
        $user_vote->time = time();
        R::store($user_vote);

        echo "Vote changed!";
        echo "<br />";
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
