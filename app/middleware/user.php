<?php

/**
 * If user is not logged in, he's redirected to login page
 *
 * @param $app
 * @param $settings
 * @return callable
 */
$authenticate = function($app) {
    return function() use ($app) {
        if (!isset($_SESSION['user'])) {
            $app->redirect('/user/login');
        }
    };
};