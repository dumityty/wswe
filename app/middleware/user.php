<?php

/**
 * If user is not logged in, he's redirected to login page
 *
 * @param $app
 * @return callable
 */
$authenticate = function($app) {
    return function() use ($app) {
        // krumo('mid');
        // krumo($_SESSION);

        if (!isset($_SESSION['user'])) {
            $app->redirect('/user/login');
        }
    };
};

/**
* If user is logged in, he is not able to visit register page, login page and will be
* redirected to admin home
*
* @param $app
* @return callable
*/
$isLogged = function($app) {
  return function() use ($app) {
    if (isset($_SESSION['user'])) {
        $app->redirect('/user');
    }
  };
};
