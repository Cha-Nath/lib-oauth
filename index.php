<?php

if (!defined('HSA_ROOT')) define('HSA_ROOT', dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR);

require_once HSA_ROOT . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use nlib\OAuth\Classes\OAuth;

// var_dump('http://localhost/hubspot/web/app/plugins/hubspot-sales-allocation/lib/OAuth/index.php');
// $oauth = new OAuth;
($oauth = new OAuth);

if (!isset($_GET['code'])) :
    $oauth->check_authorization();
elseif (empty($state = $_GET['state']) || $state !== $oauth->getOption('oauth_state')) :
    $oauth->update_field('oauth_state', '');
    die('Invalid state');
else :

    $oauth->setToken();
endif;