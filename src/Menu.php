<?php

namespace Mula;

class Menu {

    public static function createTopLevelMenu() {
        add_action('admin_menu', function() {
            /**
             * @param string $page_title
             * @param string $menu_title
             * @param string $capability
             * @param string $menu_slug
             * @param callable $function
             * @param string $icon_url
             * @param int $position
             * 
             * @return string
            */
            add_menu_page(
                'Getting Started',
                'Mula',
                'manage_options',
                Config::TOP_LEVEL_MENU_SLUG,
                function() {
                    if ( ! current_user_can( 'manage_options' ) ) {
                        return;
                    } ?>
                    <div id="mula-wordpress-index-page" class="wrap">
                        <h1><?php echo esc_html( get_admin_page_title() ) ?></h1>
                        <div id="mula-plugin-get-started-guide"></div>
                    </div>
                    <?php
                },
                Config::MENU_ICON,
                Config::MENU_POSITION
            );
        });
    }

    public static function createConfigPageSubMenu() {
        add_action('admin_menu', function() {
            add_submenu_page(
                Config::TOP_LEVEL_MENU_SLUG, 
                'Mula Setup Details',
                'Settings',
                'manage_options', 
                Config::MULA_CONFIG_SUB_MENU_SLUG,
                function() {
                ?>
                <div id="mula-wordpress-config-page" class="wrap">
                    <h1 style="display: none;"></h1>
                    <form action="options.php" method="POST">
                        <br/>
                        <div id="mula-config-form-group">
                            <div class="config-group" id="mula-configs">
                                <div>
                                    <?php do_settings_sections(Config::CONFIG_PAGE_SLUG); ?>
                                    <?php settings_fields(Config::CONFIG_PAGE_OPTION_GROUP); ?>
                                </div>
                            </div>
                            <br/>
                        </div>
                        <div id="mula-config-save-button">
                            <?php submit_button('Save Configurations'); ?>
                        </div>
                    </form>
                </div>
                <?php
            });
        });
    }
}

?>