<?php

require 'vendor/autoload.php';

require 'vendor/rb.php';

R::setup('mysql:host=localhost;
        dbname=wswe','root','titi');

$app = new \Slim\Slim(array(
    'templates.path' => '../templates',
));

$app->view(new \Slim\Views\Twig());
$app->view->parserOptions = array(
    'charset' => 'utf-8',
    'cache' => realpath('../templates/cache'),
    'auto_reload' => true,
    'strict_variables' => false,
    'autoescape' => true
);
$app->view->parserExtensions = array(new \Slim\Views\TwigExtension());

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

$app->get('/addvenue', function() {
	$venue = R::dispense('venues');
	$venue->name = 'V2';
	$venue->address = '12 abc';
	$venue->postcode = 'sg141px';
	$venue->phone = '12345678';
	$venue->email = 'abc@bla.com';
	$id = R::store($venue);
});

$app->get('/vote', function() {
	$user = 'user1';

	// check if user already voted today

	$vote = R::dispense('votes');
	$vote->venue = '1';
	$vote->user = $user;
	$vote->time = time();
	$id = R::store($vote);
});


$app->run();