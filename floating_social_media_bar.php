<?php

/*
    Plugin Name: floating social media bar
    Description: my first wordpress plugin
    Author: Issa serhan
*/

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class FloatingSocial
{
    function __construct()
    {
        add_action('admin_menu', array($this, 'adminPage'));
        add_action('admin_init', array($this, 'settings'));
        add_action('wp_enqueue_scripts', array($this, 'fsb_enqueue_scripts'));
        add_filter('the_content', array($this, 'ifWrap'));

    }
    function ifWrap($content)
    {
        if (
            is_main_query()
        ) {
            return $this->createHTML($content);
        }
        return $content;
    }
    function createHTML($content)
    {

        $backgroundColor = get_option("fsb_background_color");
        $topPosition = get_option("fsb_top_position");
        ?>
        <div class="floating-social-bar"
            style="background-color:<?php echo $backgroundColor ?>; top:<?php echo $topPosition ?> ">
            <!-- <ul class="social-media-icons"> -->
            <?php

            for ($i = 1; $i <= 5; $i++) {
                $social_media_link = get_option("fsb_social_media_link_$i");
                $social_media_icon = get_option("fsb_social_media_icon_$i");
                $social_media_icon_color = get_option("fsb_social_media_icon_color_$i");


                if (!empty($social_media_link) && !empty($social_media_icon)) {
                    echo '<div><a href="' . esc_url($social_media_link) . '" target="_blank">';
                    echo '<i class="fab ' . esc_attr($social_media_icon) . '" style="color: ' . esc_attr($social_media_icon_color) . '"></i>';

                    echo '</a></div>';
                }
            }

            ?>
            <!-- </ul> -->
        </div>
        <?php

    }
    function fsb_enqueue_scripts()
    {
        // Enqueue required stylesheets
        wp_enqueue_style('fsb-styles', plugin_dir_url(__FILE__) . 'css/style.css');

        // Enqueue FontAwesome for social media icons

        wp_enqueue_style('fontawesome', 'https://use.fontawesome.com/releases/v5.0.6/css/all.css');

        // Enqueue JavaScript file for any future functionality
        wp_enqueue_script('fsb-script', plugin_dir_url(__FILE__) . 'js/script.js', array('jquery'), '1.0', true);
    }
    function settings()
    {

        add_settings_section('fsb_general_section', null, null, 'fsb-settings');

        add_settings_field('fsb_top_position', 'Top Position', array($this, 'topPostitionHTML'), 'fsb-settings', 'fsb_general_section');
        register_setting('fsb-settings', 'fsb_top_position', array('sanitize_callback' => 'sanitize_text_field'));

        add_settings_field('fsb_background_color', 'Background Color', array($this, 'bgColorHTML'), 'fsb-settings', 'fsb_general_section');
        register_setting('fsb-settings', 'fsb_background_color', array('sanitize_callback' => 'sanitize_text_field'));

        // Register the social media blocks
        for ($i = 1; $i <= 5; $i++) {
            add_settings_section('fsb_social_media_section_' . $i, null, null, 'fsb-icon-settings');
            add_settings_field('fsb_social_media_name_' . $i, 'Social Media Name', array($this, 'fsb_social_media_name'), 'fsb-icon-settings', 'fsb_social_media_section_' . $i, array('id' => $i));
            add_settings_field('fsb_social_media_link_' . $i, 'Social Media Link', array($this, 'fsb_social_media_link'), 'fsb-icon-settings', 'fsb_social_media_section_' . $i, array('id' => $i));
            add_settings_field('fsb_social_media_icon_' . $i, 'Social Media Icon', array($this, 'fsb_social_media_icon'), 'fsb-icon-settings', 'fsb_social_media_section_' . $i, array('id' => $i));
            add_settings_field('fsb_social_media_icon_color_' . $i, 'Social Media Icon Color', array($this, 'fsb_social_media_icon_color'), 'fsb-icon-settings', 'fsb_social_media_section_' . $i, array('id' => $i));
        }
        for ($i = 1; $i <= 5; $i++) {
            register_setting('fsb-icon-settings', 'fsb_social_media_name_' . $i, array('sanitize_callback' => 'sanitize_text_field'));
            register_setting('fsb-icon-settings', 'fsb_social_media_link_' . $i, array('sanitize_callback' => 'sanitize_text_field'));
            register_setting('fsb-icon-settings', 'fsb_social_media_icon_' . $i, array('sanitize_callback' => 'sanitize_text_field'));
            register_setting('fsb-icon-settings', 'fsb_social_media_icon_color_' . $i, array('sanitize_callback' => 'sanitize_text_field'));
        }
    }
    function fsb_social_media_section($args)
    {
        // Output any description for the social media sections if needed
    }

    function fsb_social_media_name($args)
    {
        $id = $args['id'];
        $name = get_option('fsb_social_media_name_' . $id, '');
        echo '<input type="text" name="fsb_social_media_name_' . $id . '" value="' . esc_attr($name) . '" />';
    }

    function fsb_social_media_link($args)
    {
        $id = $args['id'];
        $link = get_option('fsb_social_media_link_' . $id, '');
        echo '<input type="text" name="fsb_social_media_link_' . $id . '" value="' . esc_attr($link) . '" />';
    }

    function fsb_social_media_icon($args)
    {
        $id = $args['id'];
        $icon = get_option('fsb_social_media_icon_' . $id, '');
        echo '<input type="text" name="fsb_social_media_icon_' . $id . '" value="' . esc_attr($icon) . '" />';
    }

    function fsb_social_media_icon_color($args)
    {
        $id = $args['id'];
        $icon_color = get_option('fsb_social_media_icon_color_' . $id, '#000000');
        echo '<input type="color" name="fsb_social_media_icon_color_' . $id . '" value="' . esc_attr($icon_color) . '" />';
    }
    function topPostitionHTML()
    {
        $top_position = get_option('fsb_top_position', '100px');
        echo '<input type="text" name="fsb_top_position" value="' . esc_attr($top_position) . '" />';
    }
    function bgColorHTML()
    {
        $background_color = get_option('fsb_background_color', '#000000');
        echo '<input type="color" name="fsb_background_color" value="' . esc_attr($background_color) . '" />';
    }

    function adminPage()
    {
        add_menu_page('Floating Social Media Bar', 'Floating Social Media Bar', 'manage_options', 'fsb-settings', array($this, 'adminHTML'), plugin_dir_url(__FILE__) . 'custom-icon.svg', 30);
        // we use this sub menu although it is same page sa main mein but we use ti to change admin menu name
        add_submenu_page('fsb-settings', 'Floating Social Media Bar', 'Floating Social Media ', 'manage_options', 'fsb-settings', array($this, 'adminHTML'));
        add_submenu_page('fsb-settings', 'Word Filter Options', 'Options', 'manage_options', 'fsb-settings-2', array($this, 'optionHTML'));

        //add_options_page('Floating Social Media Bar', 'Floating Social Media', 'manage_options', 'fsb-settings', array($this, 'adminHTML'));
    }

    function adminHTML()
    { ?>
        <div class="wrap">
            <h1>Floating Social Media Bar Settings</h1>
            <form method="post" action="options.php">
                <?php
                // Output required settings fields
                settings_fields('fsb-icon-settings');
                do_settings_sections('fsb-icon-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    function optionHTML()
    { ?>
        <div class="wrap">
            <h1>Settings</h1>
            <form method="POST" action="option.php">
                <?php
                settings_fields('fsb-settings');
                do_settings_sections('fsb-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}

$floatingSocialMedia = new FloatingSocial()
    ?>