<?php

namespace nlib\OAuth\Interfaces;

// use Flipbox\OAuth2\Client\Provider\HubSpot;

interface OAuthInterface {

    /**
     *
     * @param [type] $wp_did_header
     * @return self
     */
    public function wp_init(&$wp_did_header);

    /**
     *
     * @param array $fields
     * @return void
     */
    public function update_fields(array $fields);

    /**
     *
     * @param [type] $field
     * @param [type] $value
     * @return void
     */
    public function update_field($field, $value);

    /**
     *
     * @return void
     */
    public function check_authorization();

    /**
     *
     * @return HubSpot
     */
    public function getProvider();

    /**
     *
     * @return array
     */
    public function getOptions();

    /**
     *
     * @param [type] $key
     * @return mixed
     */
    public function getOption($key);
    
    /**
     *
     * @return self
     */
    public function setProvider();

    /**
     *
     * @return self
     */
    public function setOptions();

    /**
     *
     * @return void
     */
    public function setToken();
}