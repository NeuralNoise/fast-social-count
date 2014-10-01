<?php
class FSCOptionsPage
{
    /*** Holds the values to be used in the fields callbacks */
    private $options;

    /*** Start up */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }
    /*** Add options page */
    public function add_plugin_page() {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin',
            'Fast Social Count',
            'manage_options',
            'fast-social-count-admin',
            array( $this, 'create_admin_page' )
        );
    }

    /*** Options page callback */
    public function create_admin_page() {
        // Set class property
        $this->options = get_option( 'fast_social_count_option_name' );
        ?>
        <div class="wrap">

            <form method="post" action="options.php">
                <h2><?php _e('Fast Social Count Settings','fast-social-count'); ?></h2>
                <div class="postbox">
                <div class="inside">
                    <h3>[fastsocial]</h3>
                    <p><?php _e('Use in page or post with shortcode:','fast-social-count'); ?> <strong>[fastsocial]</strong> <br>
                    <?php _e('See which values you can set together whith each global setting below.','fast-social-count'); ?></p>
                </div>
            </div>
                <?php
                    // This prints out all hidden setting fields
                    settings_fields( 'fast_social_count_option_group' );
                    do_settings_sections( 'fast-social-count-admin' );
                    submit_button();
                ?>
            </form>
            <div class="postbox">
                <div class="inside">
                    <h3><?php _e('Override Fast Social Count CSS','fast-social-count'); ?></h3>
                    <p><?php _e('Change the hover for the buttons, and/or font/text-color in your own css:','fast-social-count'); ?></p>
                    <p><code>li.fsc_share_button {}</code><br><code>li.fsc_share_button:hover {}</code> </p>
                    <h3><small><?php _e('Have a look default hover setting, and the rest of the css-file:','fast-social-count'); ?> </small><a href="<?php echo plugins_url('fast-social-count.css', __FILE__); ?>">fast-social-count.css</a></h3>
                    <p><?php _e('Of course you could copy that css to your own css-file and then deregister the stylesheet in your functions.php:','fast-social-count'); ?></p>
                    <code>
                        //<?php _e('Remove plugin styles','fast-social-count'); ?><br>
                        add_action( 'wp_print_styles', 'my_deregister_styles', 100 );<br>
                        function my_deregister_styles() {<br>
                            &nbsp;&nbsp;wp_deregister_style( 'fast_social_count_css' );<br>
                        }
                    </code>
                </div>
            </div>
        </div>
        <?php


    }

    /*** Register and add settings */
    public function page_init() {
        register_setting(
            'fast_social_count_option_group', // Option group
            'fast_social_count_option_name', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'social_networks', // ID
            __("Choose social networks default values",'fast-social-count'), // Title
            array( $this, 'print_social_networks_info' ), // Callback
            'fast-social-count-admin' // Page
        );

        add_settings_field(
            'sharelabel',
            __("Share label",'fast-social-count'),
            array( $this, 'textbox_callback' ),
            'fast-social-count-admin',
            'social_networks',
            array('sharelabel','Share with your friends') //ID and placeholder text
        );
        add_settings_field(
            'class',
            __("CSS-classes for outer div",'fast-social-count'),
            array( $this, 'textbox_callback' ),
            'fast-social-count-admin',
            'social_networks',
            array('class','col-sm-10')
        );
        add_settings_field(
            'iconclass',
            __("Applies to all icons",'fast-social-count'),
            array( $this, 'textbox_callback' ),
            'fast-social-count-admin',
            'social_networks',
            array('iconclass','fa-lg your-own-color-class')
        );
        add_settings_field(
            'hassharedtext',
            __("Text about the total count",'fast-social-count'),
            array( $this, 'textbox_callback' ),
            'fast-social-count-admin',
            'social_networks',
            array('hassharedtext','persons already did!')
        );
        add_settings_field(
            'enable_totalcount',
            __("Show total count?",'fast-social-count'),
            array( $this, 'checkbox_enable' ),
            'fast-social-count-admin',
            'social_networks',
            array('enable_totalcount', 'totalcount', 'label_for' => 'enable_totalcount')
        );

        add_settings_section(
            'active_networks_section_id', // ID
            __("Which networks should be visible by default?",'fast-social-count'), // Title
            array( $this, 'print_active_networks_section_info' ), // Callback
            'fast-social-count-admin' // Page
        );

        add_settings_field(
            'enable_facebook',
            __("Activate Facebook",'fast-social-count'),
            array( $this, 'checkbox_enable' ),
            'fast-social-count-admin',
            'active_networks_section_id',
            array( 'enable_facebook', 'facebook', 'label_for' => 'enable_facebook' )
        );
        add_settings_field(
            'enable_twitter',
            __("Activate Twitter",'fast-social-count'),
            array( $this, 'checkbox_enable' ),
            'fast-social-count-admin',
            'active_networks_section_id',
            array( 'enable_twitter', 'twitter', 'label_for' => 'enable_twitter' )
        );
        add_settings_field(
            'enable_googleplus',
            __("Activate Google+",'fast-social-count'),
            array( $this, 'checkbox_enable' ),
            'fast-social-count-admin',
            'active_networks_section_id',
            array( 'enable_googleplus', 'googleplus', 'label_for' => 'enable_googleplus' )
        );
        add_settings_field(
            'enable_linkedin',
            __("Activate LinkedIn",'fast-social-count'),
            array( $this, 'checkbox_enable' ),
            'fast-social-count-admin',
            'active_networks_section_id',
            array( 'enable_linkedin', 'linkedin', 'label_for' => 'enable_linkedin' )
        );
        add_settings_field(
            'enable_pinterest',
            __("Activate Pinterest",'fast-social-count'),
            array( $this, 'checkbox_enable' ),
            'fast-social-count-admin',
            'active_networks_section_id',
            array( 'enable_pinterest', 'pinterest', 'label_for' => 'enable_pinterest')
        );

        add_settings_section(
            'add_to_all_section', // ID
            __("Display in footer on all pages",'fast-social-count'), // Title
            array( $this, 'print_add_to_all_section_info' ), // Callback
            'fast-social-count-admin' // Page
        );
        add_settings_field(
            'cachetimer',
            __("Choose a long cache time",'fast-social-count'),
            array( $this, 'cache_callback' ),
            'fast-social-count-admin',
            'active_networks_section_id'
        );

        add_settings_field(
            'add_to_all_footers',
            __(" No! Do not check this box",'fast-social-count'),
            array( $this, 'checkbox_enable' ),
            'fast-social-count-admin',
            'add_to_all_section',
            array( 'add_to_all_footers', 'label_for' => 'add_to_all_footers' )
        );
    }

    /*** Sanitize each setting field as needed
     * @param array $input Contains all settings fields as array keys */
    public function sanitize( $input ) {
        $new_input = array();

        if( isset( $input['sharelabel'] ) )
            $new_input['sharelabel'] = sanitize_text_field( $input['sharelabel'] );
        if( isset( $input['class'] ) )
            $new_input['class'] = sanitize_text_field( $input['class'] );
        if( isset( $input['iconclass'] ) )
            $new_input['iconclass'] = sanitize_text_field( $input['iconclass'] );
        if( isset( $input['hassharedtext'] ) )
            $new_input['hassharedtext'] = sanitize_text_field( $input['hassharedtext'] );
        if( isset( $input['enable_totalcount'] ) )
            $new_input['enable_totalcount'] = absint( $input['enable_totalcount'] );

        if( isset( $input['enable_facebook'] ) )
            $new_input['enable_facebook'] = absint( $input['enable_facebook'] );
        if( isset( $input['enable_twitter'] ) )
            $new_input['enable_twitter'] = absint( $input['enable_twitter'] );
        if( isset( $input['enable_googleplus'] ) )
            $new_input['enable_googleplus'] = absint( $input['enable_googleplus'] );
        if( isset( $input['enable_linkedin'] ) )
            $new_input['enable_linkedin'] = absint( $input['enable_linkedin'] );
        if( isset( $input['enable_pinterest'] ) )
            $new_input['enable_pinterest'] = absint( $input['enable_pinterest'] );
        if( isset( $input['cachetimer'] ) )
            $new_input['cachetimer'] = absint( $input['cachetimer'] );
        if( isset( $input['add_to_all_footers'] ) )
            $new_input['add_to_all_footers'] = absint( $input['add_to_all_footers'] );

        return $new_input;
    }

    /*** Print the Section text */
    public function print_social_networks_info() {
        print __("This settings can be overridden with shortcode values.",'fast-social-count') . '<br>';
    }
    public function print_active_networks_section_info() {
        $aboutcache = '<br><br>' . __("Select how often networks should be checked for new shares. Default is 1 hour.",'fast-social-count') . '<br>' . __("If your web host support WP Object Cache, then caching of the counts will work. Read more",'fast-social-count') . ' <a href="http://codex.wordpress.org/Class_Reference/WP_Object_Cache" target="_blank">codex.wordpress.org/Class_Reference/WP_Object_Cache</a>';
        print __("Check each network that should be visible by default. Values added to shortcode overrides theese values. ",'fast-social-count') . $aboutcache;
    }
    public function print_add_to_all_section_info() {
        print __('Not recommended, better to choose wich pages to promote and add the social buttons with shortcode on places you think is best for that particular page/post. If you still want to do it, make sure you add css-classes as needed to the outer div. ( .. class="your-container-class")','fast-social-count');
    }

    /*** Get the settings option array and print one of its values */
    public function textbox_callback($args) { // Textbox Callback
        printf(
            '<input type="text" id="' . $args[0] . '" class="regular-text" placeholder="' . $args[1] . '" name="fast_social_count_option_name[' . $args[0] . ']" value="%s" /><br><small>[fastsocial ' . $args[0] . '="' . $args[1] . '"]</small>',
            isset( $this->options[$args[0]] ) ? esc_attr( $this->options[$args[0]]) : ''
        );
    }

    public function checkbox_enable($args) { // checkbox Callback
        $options = get_option( 'fast_social_count_option_name' ); ?>
        <input id="<?php echo $args[0]; ?>" name="fast_social_count_option_name[<?php echo $args[0]; ?>]"  type="checkbox" value="1" <?php checked($options[$args[0]], 1  ) ; ?> />
        <?php
        if ( "" != $args[1] ) { ?>
            <small>[fastsocial <?php echo $args[1]; ?>="yes"]</small>
         <?php
        }

    }

    public function cache_callback() { // select Callback
        $options = get_option( 'fast_social_count_option_name' ); ?>
        <select id="cachetimer" name="fast_social_count_option_name[cachetimer]">
            <option value="3600" <?php selected($options['cachetimer'], 3600  ) ; ?>>1 <?php _e('hour','fast-social-count'); ?></option>
            <option value="43200" <?php selected($options['cachetimer'], 43200  ) ; ?>>12 <?php _e('hours','fast-social-count'); ?></option>
            <option value="86400" <?php selected($options['cachetimer'], 86400  ) ; ?>>24 <?php _e('hours','fast-social-count'); ?></option>
            <option value="600" <?php selected($options['cachetimer'], 600  ) ; ?>>10 <?php _e('min','fast-social-count'); ?></option>
            <option value="1800" <?php selected($options['cachetimer'], 1800  ) ; ?>>30 <?php _e('min','fast-social-count'); ?></option>
        </select>
        <?php
    }

} // end class FSCOptionsPage

if( is_admin() )
    $fast_social_count_settings_page = new FSCOptionsPage();

