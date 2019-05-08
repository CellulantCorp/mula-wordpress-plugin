<?php
    require dirname(__FILE__).'/src/Config.php';
    use Mula\Config;

    if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
        exit;
    }

    $options = [
        Config::IV_KEY_TEXT_INPUT,
        Config::ACCESS_KEY_TEXT_INPUT,
        Config::SECRET_KEY_TEXT_INPUT,
        Config::DUE_DATE_NUMBER_INPUT,
        Config::SERVICE_CODE_TEXT_INPUT,
        Config::COUNTRY_CODE_SELECT_INPUT,
        Config::CHECKOUT_TYPE_SELECT_INPUT
    ];

    foreach($options as $key => $option) {
        delete_option($option);
    }
?>