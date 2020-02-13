<?php

namespace nlib\OAuth\Classes;

use Flipbox\OAuth2\Client\Provider\HubSpot;
use nlib\OAuth\Interfaces\OAuthInterface;

class OAuth implements OAuthInterface {

    private $_provider;
    private $_options;
    private $_fields = [
        'client_id',
        'client_secret',
        'scope',
        'portal_id',
        'api_key',
        'oauth_code',
        'oauth_state',
        'oauth_token',
        'oauth_refresh_token',
        'oauth_expire_token',
    ];

    public function __construct() {
        
        global $wp_did_header;

        $this->wp_init($wp_did_header)->setOptions()->setProvider();
    }

    public function wp_init(&$wp_did_header) {
        
        if (!isset($wp_did_header)) :
    
            $wp_did_header = true;
            
            // Load the WordPress library.
            define( 'WP_ROOT', dirname(dirname(dirname(dirname(dirname(dirname(__DIR__)))))) );
            require_once WP_ROOT . '/wp/wp-load.php';
            // var_dump($_SERVER['DOCUMENT_ROOT']);
            // require_once 'localhost/hubspot/web/wp/wp-load.php';
            // http://localhost.hubspot/app/plugins/hubspot-sales-allocation/lib/oauth/index.php
            
            // Set up the WordPress query.
            wp();
        endif;

        return $this;
    }

    public function update_fields(array $fields) {

        if(!empty($fields)) foreach($fields = $fields as $key => $value)
            $this->update_field($key, $value);
    }

    public function update_field($field, $value) {

        $values = [ $field => $value ];
        update_field( 'hubspot', $values, 'option' );
        $this->setOptions();
    }

    public function check_authorization() {

        $options = [
            'scope' => $this->getOption('scope'),
        ];
    
        $authorizationUrl = $this->getProvider()->getAuthorizationUrl($options);
        $this->update_field('oauth_state', $this->getProvider()->getState());

        header('Location: ' . $authorizationUrl);
        exit;
    }

    #region Getter

    public function getProvider() { return $this->_provider; }
    public function getOptions() { return $this->_options; }

    public function getOption($key) { return array_key_exists($key, $this->_options) ? $this->_options[$key] : ''; }

    #endregion

    #region Setter
    
    public function setProvider() {

        $hubspot_options = $this->getOptions();

        $this->_provider = new HubSpot([
            'clientId' => $hubspot_options['client_id'],
            'clientSecret' => $hubspot_options['client_secret'],
            'redirectUri' => plugins_url() . '/hubspot-sales-allocation/lib/OAuth/index.php',
            // 'redirectUri' => 'http://localhost/hubspot/web/app/plugins/hubspot-sales-allocation/lib/OAuth/index.php',
            // 'scope' => $scope
        ]);

        return $this;
    }

    public function setOptions() {

        if (!function_exists('get_field') || empty($hubspot_options = get_field('hubspot', 'option'))) exit('Initial error');

        $missings = [];

        foreach( $this->_fields as $field )
            if(!array_key_exists($field, $hubspot_options)) $missings[] = $field;

        if(!empty($missings)) die('Missing field (s) : "' . implode(',', $missings) . '"');

        $this->_options = $hubspot_options;

        return $this;
    }

    public function setToken() {
        // Try to get an access token (using the authorization code grant)
        $token = $this->getProvider()->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);

        // Optional: Now you have a token you can look up a users profile data
        try {

            // We got an access token, let's now get the user's details
            $user = $this->getProvider()->getResourceOwner($token);

            // Use these details to create a new profile
            printf('Hello %s !', $user->getEmail());
            echo '<br/>';
        } catch (\Exception $e) {

            // Failed to get user details
            die('Oh dear...');
        }
        
        $this->update_fields([
            'oauth_state' => '',
            'oauth_token' => $token->getToken(),
            'oauth_refresh_token' => $token->getRefreshToken(),
            'oauth_expire_token' => $token->getExpires(),
        ]);

        var_dump($this->getOptions());

        // header('Location: ' . get_home_url() . '/wp/wp-admin/admin.php?page=hubspot-sales-allocation-options');
    }
    
    #endregion
}