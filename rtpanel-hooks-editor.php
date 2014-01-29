<?php
/*
Plugin Name: rtPanel Hooks Editor
Description: his plugin adds hooks-editing interface in theme options for "rtPanel Theme Framework"
Version: 2.4
Author: rtcamp
Author URI: http://rtcamp.com
Contributors: rtCampers ( http://rtcamp.com/about/rtcampers/ )
License: GNU General Public License, v2 (or newer)
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

// Setup default values on plugin activation
register_activation_hook( __FILE__, 'default_values' );

/** 
 * Register rtPanel Hooks Editor with the Settings API
 * 
 * @since rtPanel Hooks Editor 1.0
 */
function rtp_register_hooks() {
    register_setting( 'hook_settings', 'rtp_hooks', 'rtp_hooks_validate');
}
add_action( 'admin_init', 'rtp_register_hooks' );

/** 
 * Default Values
 * 
 * @since rtPanel Hooks Editor 1.0
 */
function default_values() {
    $default_hooks = array(
        'head'                      => '',
        'begin_body'                => '',
        'begin_main_wrapper'        => '',
        'before_header'             => '',
        'begin_primary_menu'        => '',
        'end_primary_menu'          => '',
        'begin_header'              => '',
        'before_logo'               => '',
        'after_logo'                => '',
        'end_header'                => '',
        'after_header'              => '',
        'before_content_wrapper'    => '',
        'begin_content_wrapper'     => '',
        'begin_content_row'         => '',
        'begin_content'             => '',
        'begin_post'                => '',
        'begin_post_title'          => '',
        'end_post_title'            => '',
        'begin_post_meta_top'       => '',
        'end_post_meta_top'         => '',
        'post_meta_top'             => '',
        'begin_post_content'        => '',
        'end_post_content'          => '',
        'begin_post_meta_bottom'    => '',
        'end_post_meta_bottom'      => '',
        'post_meta_bottom'          => '',
        'end_post'                  => '',
        'single_pagination'         => '',
        'comments'                  => '',
        'archive_pagination'        => '',
        'end_content'               => '',
        'sidebar'                   => '',
        'begin_sidebar'             => '',
        'sidebar_content'           => '',
        'end_sidebar'               => '',
        'end_content_wrapper'       => '',
        'after_content_wrapper'     => '',
        'end_content_row'           => '',
        'before_footer'             => '',
        'begin_footer'              => '',
        'end_footer'                => '',
        'after_footer'              => '',
        'end_main_wrapper'          => '',
        'end_body'                  => '',
    );

    if ( !get_option( 'rtp_hooks' ) ) {
        update_option( 'rtp_hooks', $default_hooks );
        $blog_users = get_users();

        /* Set screen layout to 1 by default for all users */
        foreach ( $blog_users as $blog_user ) {
          $blog_user_id = $blog_user->ID;
          if ( !get_user_meta( $blog_user_id, 'screen_layout_appearance_page_rtp_hooks' ) )
          update_user_meta( $blog_user_id, 'screen_layout_appearance_page_rtp_hooks', 1, NULL );
        }
    }

    return $default_hooks;
}

function rtp_hooks_admin_notice(){
    $is_rtpanel = false;
    if ( 'rtpanel' == get_template()  ) {
        $is_rtpanel = true;
    }
    if ( !$is_rtpanel ) {
        echo '<div class="error"><p>' . __( 'You need to use rtPanel Theme Framework to make use of this rtPanel Hooks Editor.', 'rtPanel' ) . '</p></div>';
    }
}
add_action( 'admin_notices', 'rtp_hooks_admin_notice' );

/** 
 * Hook the rtPanel Hooks Editor to rtPanel
 * 
 * @since rtPanel Hooks Editor 1.0
 */
function rtp_hooks( $theme_pages ) {
    $theme_pages['rtp_hooks'] = array(
        'menu_title'    => __( 'Hooks', 'rtPanel' ),
        'menu_slug'     => 'rtp_hooks'
    );
    return $theme_pages;
}
add_filter( 'rtp_add_theme_pages', 'rtp_hooks' );

/** 
 * Validate the Hooks
 * 
 * @since rtPanel Hooks Editor 1.0
 */
function rtp_hooks_validate( $input ) {
    if ( isset ( $_POST['rtp_reset'] ) ) {
       $input = default_values();
       add_settings_error( 'rtp_hooks', 'reset_rtp_hooks', __( 'All rtPanel Hooks have been restored to default.', 'rtPanel' ), 'updated' );
    }
    return $input;
}

/** 
 * Contextual Help for rtPanel Hooks Editor
 * 
 * @since rtPanel Hooks Editor 1.0
 */
function rtp_hooks_screen_options() {
    add_meta_box( 'hook_options', __( 'Hook Options', 'rtPanel' ), 'rtp_hooks_metabox', 'appearance_page_rtp_hooks', 'normal', 'core' );
}
add_action( 'rtp_hooks_metaboxes', 'rtp_hooks_screen_options' );

function rtp_hook_help() {
    $contextual_help = '<p>';
    $contextual_help .= __( 'With rtPanel Hook Plugin you can write the code for all the action hooks available in rtPanel from you rtPanel Admin.', 'rtPanel' );
    $contextual_help .= '</p><p>';
    $contextual_help .= __( 'Have a look at all the hooks provided by rtPanel <a href="http://rtcamp.com/rtpanel/docs/developer/" title="rtPanel Hooks">here</a>.', 'rtPanel' );
    $contextual_help .= '</p>';
    
    $screen = get_current_screen();
    $screen->add_help_tab( array( 'title' => __( 'Hooks', 'rtPanel' ), 'id' => 'rtp-hooks-help', 'content' => $contextual_help ) );

}
add_action( 'load-appearance_page_rtp_hooks', 'rtp_theme_options_help' );
add_action( 'load-appearance_page_rtp_hooks', 'rtp_hook_help' );
add_action( 'load-appearance_page_rtp_general', 'rtp_hook_help', 11 );
add_action( 'load-appearance_page_rtp_post_comments', 'rtp_hook_help', 11 );

// Get the rtPanel Hooks Editor options from database
$rtp_hooks = get_option( 'rtp_hooks' );

/** 
 * rtPanel Hooks Editor Page
 * 
 * @since rtPanelChild 1.0
 */
function rtp_hooks_options_page( $pagehook ) {
    global $screen_layout_columns; ?>

    <div class="options-main-container">
        <?php settings_errors(); ?>
        <a href="#" class="expand-collapse button-link" title="<?php _e( 'Show/Hide All', 'rtPanel' ); ?>"><?php _e( 'Show/Hide All', 'rtPanel' ); ?></a>
        <div class="clear"></div>
        <div class="options-container">
            <form name="rt_hooks_form" id="rt_hooks_form" action="options.php" method="post" enctype="multipart/form-data">
                <?php
                /* nonce for security purpose */
                wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
                wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>

                <input type="hidden" name="action" value="save_rtp_metaboxes_hooks" />
                <div id="poststuff" class="metabox-holder alignleft <?php echo 2 == $screen_layout_columns ? ' has-right-sidebar' : ''; ?>">
                    <div id="side-info-column" class="inner-sidebar">
                        <?php do_meta_boxes( $pagehook, 'side', '' ); ?>
                    </div>
                    <div id="post-body" class="has-sidebar">
                        <div id="post-body-content" class="has-sidebar-content">
                            <?php settings_fields( 'hook_settings' ); ?>
                            <?php do_meta_boxes( $pagehook, 'normal', '' ); ?>
                        </div>
                    </div>
                    <br class="clear"/>
                    <input class="button-primary" value="<?php _e( 'Save All Changes', 'rtPanel' ); ?>" name="rtp_submit" type="submit" />
                    <input class="button-link" value="<?php _e( 'Reset All Hooks Settings', 'rtPanel' ); ?>" name="rtp_reset" type="submit" />
                </div>

                <script type="text/javascript">
                    //<![CDATA[
                    jQuery(document).ready( function($) {
                        // close postboxes that should be closed
                        $('.if-js-closed').removeClass('if-js-closed').addClass('closed');
                        // postboxes setup
                        postboxes.add_postbox_toggles('<?php echo $pagehook; ?>');
                    });
                    //]]>
                </script>
            </form>
        </div>
    </div><?php
}

/** 
 * rtPanel Hooks Editor Metaboxes ( Screen Options )
 * 
 * @since rtPanelChild 1.0
 */
function rtp_hooks_metabox() {
        global $rtp_hooks; ?>
            <br />
            <strong><?php _e( 'Have a look at all the hooks available in rtPanel', 'rtPanel' ); ?> -> <a target="_blank" href="http://rtcamp.com/rtpanel/docs/developer/" title="<?php _e( 'rtPanel Hooks', 'rtPanel' ); ?>">http://rtcamp.com/rtpanel/docs/developer/</a></strong>
            <br />
            <br />
            <table class="form-table">
                <tbody><?php
                    $admin_hooks = default_values();
                    $count = 1;
                    foreach( $admin_hooks as $option_name => $value ) {
                        $get_label = explode( '_', $option_name );
                        $label = NULL;
                        foreach( $get_label as $part_label ) {
                            if ( $label )
                                $label .= ' '.ucfirst( $part_label );
                            else
                                $label = ucfirst( $part_label );
                        } ?>
                        <tr valign="top">
                            <th scope="row"><p><label for="<?php echo $option_name; ?>"><?php _e( $label, 'rtPanel' ); ?><br/><em><?php echo ($option_name == 'head') ? 'rtp_' : 'rtp_hook_'; ?><?php echo $option_name;  ?></em></label></p></th>
                            <td><textarea cols="70" rows="7" name="rtp_hooks[<?php echo $option_name; ?>]" id="<?php echo $option_name; ?>"><?php echo isset( $rtp_hooks[$option_name] ) ? $rtp_hooks[$option_name] : ''; ?></textarea><br /></td>
                        </tr><?php
                            if( !( $count % 2 ) ) { ?>
                            <tr>
                                <td colspan="2">
                                    <div class="rtp_submit">
                                        <input class="button-primary" value="<?php _e( 'Save All Changes', 'rtPanel' ); ?>" name="rtp_submit" type="submit" />
                                        <input class="button-link" value="<?php _e( 'Reset All Hooks Settings', 'rtPanel' ); ?>" name="rtp_reset" type="submit" />
                                        <div class="clear"></div>
                                    </div>
                                </td>
                            </tr><?php
                                } ?>
                        <?php
                        $count++;
                    } ?>
                </tbody>
            </table>
            <?php
}

/**
 * Funtion to evalutate and execute php code
 * 
 * @since rtPanelChild 1.0
 */
function rtp_eval_php( $code ) {
    ob_start();
    eval("?>$code<?php ");
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
}

if( isset( $rtp_hooks ) && $rtp_hooks ) {
    /* Output the markup */
    foreach ( $rtp_hooks as $hook_name => $code ) {
        if ( !empty( $code ) ) {
            if ( $hook_name == 'head' )
                add_action( 'rtp_' . $hook_name, create_function('', 'echo rtp_eval_php( "' . addslashes ( stripslashes( $code ) ) . '" );') );
            else
                add_action( 'rtp_hook_' . $hook_name, create_function('', 'echo rtp_eval_php( "' . addslashes ( stripslashes( $code ) ) . '" );') );
        }
    }
}