<?php

namespace Mula;

class Config {
    const LOG_FILE = __DIR__.'../mula.log';

    //menus
    const MENU_POSITION = 20;
    const MENU_ICON = 'dashicons-store';
    const MULA_CONFIG_SUB_MENU_SLUG = 'mula_config_sub_menu';
    const TOP_LEVEL_MENU_SLUG = 'mula_plugin_top_level_menu';

    //assets
    const AJAX_OBJECT = 'MULA_PLUGIN_AJAX_OBJ';
    const ADMIN_PAGE_STYLE_HANDLE = 'mula-admin-styles';
    const CHECKOUT_PAGE_JS_SCRIPT = 'mula-checkout-page-js';
    const CHECKOUT_PAGE_STYLE_HANDLE = 'mula-checkout-page-styles';
    const BABEL_CDN = 'https://unpkg.com/@babel/standalone/babel.min.js';
    const REACT_CDN = 'https://unpkg.com/react@16/umd/react.production.min.js';
    const REACT_DOM_CDN = 'https://unpkg.com/react-dom@16/umd/react-dom.production.min.js';
    const MULA_CHECKOUT_LIBRARY = 'https://beep2.cellulant.com:9212/checkout/v2/mula-checkout.js';
    const MATERIAL_UI_CDN = 'https://unpkg.com/@material-ui/core/umd/material-ui.production.min.js';

    const INPUT_FIELD_PREFIX = 'WP_mula_plugin_';

    //plugin
    const IV_KEY_TEXT_INPUT = self::INPUT_FIELD_PREFIX.'iv_key';
    const SECRET_KEY_TEXT_INPUT = self::INPUT_FIELD_PREFIX.'secret_key';
    const ACCESS_KEY_TEXT_INPUT = self::INPUT_FIELD_PREFIX.'access_key';
    const DUE_DATE_NUMBER_INPUT = self::INPUT_FIELD_PREFIX.'due_date';
    const SERVICE_CODE_TEXT_INPUT = self::INPUT_FIELD_PREFIX.'service_code';
    const COUNTRY_CODE_SELECT_INPUT = self::INPUT_FIELD_PREFIX.'country_code';
    const CHECKOUT_TYPE_SELECT_INPUT = self::INPUT_FIELD_PREFIX.'checkout_type';

    //public pages
    const CHECKOUT_PAGE_SLUG = 'mula-checkout';
    const FAILED_REDIRECT_PAGE_SLUG = 'mula-fail-redirect';
    const SUCCESS_REDIRECT_PAGE_SLUG = 'mula-success-redirect';

    //admin pages
    const CONFIG_PAGE_SLUG = 'mula-config-page';
    const CONFIG_PAGE_SECTION = 'mula-config-section';
    const CONFIG_PAGE_OPTION_GROUP = 'mula-config-option-group';

    //checkout types
    const CHECKOUT_TYPES = ['modal', 'express'];

    //list of supported countries
    const COUNTRIES = [
        "kenya" => [
            "currencyCode" => "KES",
            "countryCode" => "KE"
        ],
        "tanzania" => [
            "currencyCode" => "TZS",
            "countryCode" => "TZ"
        ],
        "uganda" => [
            "currencyCode" => "UGX",
            "countryCode" => "UG"
        ],
        "ghana" => [
            "currencyCode" => "GHS",
            "countryCode" => "GH"
        ],
        "zambia" => [
            "currencyCode" => "ZMW",
            "countryCode" => "ZM"
        ],
        "zimbabwe" => [
            "currencyCode" => "USD",
            "countryCode" => "ZW"
        ]
    ];
}

?>