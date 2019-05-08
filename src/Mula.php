<?php

namespace Mula;


class Mula {
    
    public static function init() {

        add_action('admin_init', function() {
            add_settings_section(Config::CONFIG_PAGE_SECTION, 'Mula Setup Details', null, Config::CONFIG_PAGE_SLUG);
            self::createConfigPageInput('text',Config::SERVICE_CODE_TEXT_INPUT, 'SERVICE CODE');
            self::createConfigPageInput('text',Config::ACCESS_KEY_TEXT_INPUT, 'ACCESS KEY');
            self::createConfigPageInput('text',Config::IV_KEY_TEXT_INPUT, 'IV KEY');
            self::createConfigPageInput('text',Config::SECRET_KEY_TEXT_INPUT, 'SECRET KEY');
            self::createConfigPageInput('number',Config::DUE_DATE_NUMBER_INPUT, 'REQUEST EXPIRY PERIOD', '(Time in minutes)');
            self::createConfigPageSelect(Config::CHECKOUT_TYPE_SELECT_INPUT, 'CHECKOUT TYPE', array_map(function($item) {
                return ["name" => ucfirst($item), "value" => $item];
            }, Config::CHECKOUT_TYPES));
            self::createConfigPageSelect(Config::COUNTRY_CODE_SELECT_INPUT, 'COUNTRY OF OPERATION', array_map(function($item, $key) {
                return ["name" => ucfirst($key), "value" => $item["countryCode"]];
            }, Config::COUNTRIES, array_keys(Config::COUNTRIES)));
        });

        Menu::createTopLevelMenu();
        Menu::createConfigPageSubMenu();
    }

    /**
     * @param string $type
     * @param string $identifier
     * @param string $label
     * @param string $help
     */
    private static function createConfigPageInput($type, $identifier, $label, $help = '') {
        add_settings_field(
            $identifier,
            '<label for="'.$identifier.'">'.$label.'</label><br><small>'.$help.'</small>',
            function() use($type, $identifier){
                switch ($type):
                    case 'text':
                        echo '<input type="text" class="mula-input-field" name="'.$identifier.'" value="'.get_option($identifier).'" required/>';
                        break;
                    case 'number':
                        echo '<input type="number" class="mula-input-field" name="'.$identifier.'" value="'.get_option($identifier).'" required/>';
                        break;
                endswitch;
            },
            Config::CONFIG_PAGE_SLUG,
            Config::CONFIG_PAGE_SECTION );
        register_setting(Config::CONFIG_PAGE_OPTION_GROUP, $identifier);
    }

    /**
     * @param string $identifier
     * @param string $label
     * @param array $choices
     */
    private static function createConfigPageSelect($identifier, $label, $choices) {
        add_settings_field(
            $identifier,
            '<label for"'.$identifier.'">'.$label.'</label>',
            function () use($identifier, $choices) {
                $options = '<option> -- Select -- </option>';
                foreach($choices as $key => $choice) {
                    if (get_option($identifier) == $choice["value"]) :
                        $options.='<option selected value="'.$choice["value"].'">'.$choice["name"].'</option>';
                    else:
                        $options.='<option value="'.$choice["value"].'">'.$choice["name"].'</option>';
                    endif;
                }
                echo '<select name="'.$identifier.'" class="mula-select-field" required>'.$options.'</select>';
            },
            Config::CONFIG_PAGE_SLUG,
            Config::CONFIG_PAGE_SECTION
        );
        register_setting(Config::CONFIG_PAGE_OPTION_GROUP, $identifier);
    }
}

?>
