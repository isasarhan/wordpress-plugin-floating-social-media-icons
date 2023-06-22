<?php

/*
    Plugin Name: Unique plugin
    Description: my new description
    Version: 1.0
    Author: Issa Serhan    
*/


class FirstWordPressPlugin
{

    function __construct()
    {
        //adds to the setting menu the plugin name
        //we use array instead of this because we dont want to call the function
        add_action('admin_menu', array($this, 'adminPage'));

        //admin_init is triggered before any other hook when a user access the admin area. This hook doesn't provide any parameters, 
        //so it can only be used to callback a specified function.
        add_action('admin_init', array($this, 'settings'));

        // filter content 
        //  knows that in order to display the content of a page/post they can use the_content() function.
        add_filter('the_content', array($this, 'ifWrap'));
    }

    function ifWrap($content)
    {
        //is_single indicates that this page is a post 
        // if check boxes checked

        if ((is_main_query() and is_single()) and (get_option('wcp_word_count', 1))) {
            // different between $this->createHTML &&   array($this, 'ifWrap') is that here we want to call the function
            // and not pass a refernce to the function
            return $this->createHTML($content);
        }
        return $content;
    }
    function createHTML($content)
    {
        $html = '<h3>' . get_option('wcp_title', 'Post default title') . '</h3>';

        if (get_option('wp_location', '0') == '0') {
            return $html . $content;
        }
        return $content . $html;
    }

    function settings()
    {
        //creating the section 
        //4th arg is page slug  
        //dont include capital letters 
        add_settings_section('wcp_first_section', null, null, 'my-new-plugin');
        // build html for the field
        // last fild crreated name for section
        add_settings_field('wcp_location', 'Display Section', array($this, 'locationHTML'), 'my-new-plugin', 'wcp_first_section');
        // 1st arg is to put it in a grouped name for plugin 
        // 2nd option for name in database
        // 3rd option for default value
        register_setting('wordCountPlugin', 'wcp_location', array('sanitize_callback' => array($this, 'sanitizeLocation'), 'default' => '0'));


        add_settings_field('wcp_title', 'Post Title', array($this, 'titleHTML'), 'my-new-plugin', 'wcp_first_section');
        register_setting('wordCountPlugin', 'wcp_title', array('sanitize_callback' => 'sanitize_text_field', 'default' => '0'));

        add_settings_field('wcp_word_count', 'Word Count', array($this, 'wordCountHTML'), 'my-new-plugin', 'wcp_first_section');
        register_setting('wordCountPlugin', 'wcp_word_count', array('sanitize_callback' => 'sanitize_text_field', 'default' => '1'));
    }

    function sanitizeLocation($input)
    {
        if ($input != '1' and $input != '0') {
            add_settings_error('wp_location', 'wcp_location_error', 'Invalid input value');
            return get_option('wp_location');
        }
        return $input;
    }
    function wordCountHTML()
    {
        ?>
        <!-- 1 is for true 0 is false  -->
        <input type="checkbox" name="wcp_word_count" value="1" <?php checked(get_option('wcp_word_count'), '1') ?>>
        <?php
    }
    function titleHTML()
    {
        ?>
        <input type="text" name="wcp_title" value="<?php echo esc_attr(get_option('wcp_title')) ?>" />
        <?php
    }
    function locationHTML()
    { ?>
        <!-- name same as registered setting name in db -->
        <select name="wcp_location">
            <option value="0" <?php selected(get_option('wcp_location', '0')) ?>>Beginning of Post</option>
            <option value="1" <?php selected(get_option('wcp_location', '1')) ?>>End of Post</option>
        </select>
        <?php
    }
    function adminPage()
    {
        //thiss add_option_page sets attributes for the admin part
        // manage options is to allow only admin users to view this page
        add_options_page('First Plugin', 'New Plugin', 'manage_options', 'my-new-plugin', array($this, 'adminHTML'));

    }

    function adminHTML()
    { ?>
        <div class="wrap">
            <h1>Word Count Settings</h1>
            <form action="options.php" method="POST">
                <?php
                //add approtriate html hidden fields
                settings_fields('wordCountPlugin');
                //provite page slug to generate all fields and sections for this page
                do_settings_sections('my-new-plugin');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}


$firstWPPlugin = new FirstWordPressPlugin();
?>