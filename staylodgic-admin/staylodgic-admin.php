<?php
/**
 * Plugin Name: Staylodgic Site Admin
 * Plugin URI:  http://yourwebsite.com/
 * Description: Manage site settings for Staylodgic in the network admin.
 * Version:     1.0.0
 * Author:      Your Name
 * Author URI:  http://yourwebsite.com/
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Network:     true
 */

namespace StaylodgicAdmin;

class SiteAdmin
{

    public function __construct()
    {

        add_filter( 'network_edit_site_nav_links', array($this, 'staylodgicadmin_siteadmin_siteinfo_tab'));

        add_action( 'network_admin_menu', array($this, 'staylodgicadmin_siteadmin_new_options'));

        add_action( 'current_screen', array($this, 'staylodgicadmin_siteadmin_page_title'));

        add_action( 'network_admin_edit_staylodgicupdate',  array($this, 'staylodgicadmin_siteadmin_save'));

        add_action( 'network_admin_notices', array($this, 'staylodgicadmin_notice'));
    
    }

    function staylodgicadmin_notice() {

        if( isset( $_GET[ 'updated' ] ) && isset( $_GET[ 'page' ] ) && 'staylodgic' === $_GET[ 'page' ] ) {
    
            ?>
                <div id="message" class="updated notice is-dismissible">
                    <p>Congratulations!</p>
                    <button type="button" class="notice-dismiss">
                        <span class="screen-reader-text">Dismiss this notice.</span>
                    </button>
                </div>
            <?php
    
        }
    
    }

    function staylodgicadmin_siteadmin_save() {

        $id = absint( $_POST[ 'id' ] );
    
        check_admin_referer( 'staylodgic-network-check' . $id ); // nonce check
    
        update_blog_option( $id, 'site_max_rooms', sanitize_text_field( $_POST[ 'site_max_rooms' ] ) );

        wp_safe_redirect( 
            add_query_arg( 
                array(
                    'page' => 'staylodgic',
                    'id' => $id,
                    'updated' => 'true'
                ), 
                network_admin_url( 'sites.php' )
            )
        );
        exit;
    
    }

    function staylodgicadmin_siteadmin_page_title( $current_screen ) {
	
        global $title;
    
        if( 'sites_page_staylodgic-network' === $current_screen->id && isset( $_GET[ 'id' ] ) && $_GET[ 'id' ] ) {
            $blog_details = get_blog_details( array( 'blog_id' => $_GET[ 'id' ] ) );
            $title = __( 'Edit Site:' ) . ' ' . $blog_details->blogname;
        }
    }

    public function staylodgicadmin_siteadmin_siteinfo_tab( $tabs ){
    
        $tabs[ 'site-staylodgicsite' ] = array(
            'label' => 'Staylodgic',
            'url' => add_query_arg( 'page', 'staylodgic', 'sites.php' ), 
            'cap' => 'manage_sites'
        );
        return $tabs;
    
    }

    public function staylodgicadmin_siteadmin_new_options(){
        add_submenu_page( '', 'Edit site', 'Edit site', 'manage_network_options', 'staylodgic', array($this, 'staylodgicadmin_siteadmin_options_callback'));
    }

    public function staylodgicadmin_siteadmin_options_callback(){

        // do not worry about that, we will check it too
        $id = absint( $_REQUEST[ 'id' ] );
    
        $site = get_site( $id );
    
        ?>
            <div class="wrap">
                <h1 id="edit-site">Edit Site: <?php echo $site->blogname ?></h1>
                <p class="edit-site-actions">
                    <a href="<?php echo esc_url( get_home_url( $id, '/' ) ) ?>">Visit</a> | <a href="<?php echo esc_url( get_admin_url( $id ) ) ?>">Dashboard</a>
                </p>
                <?php
                    // navigation tabs
                    network_edit_site_nav(
                        array(
                            'blog_id'  => $id,
                            'selected' => 'site-staylodgicsite' // current tab
                        )
                    );
                ?>
                <form method="post" action="edit.php?action=staylodgicupdate">
                    <?php wp_nonce_field( 'staylodgic-network-check' . $id ); ?>
                    <input type="hidden" name="id" value="<?php echo $id ?>" />
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="site_max_rooms">Max Rooms</label></th>
                            <td><input name="site_max_rooms" class="regular-text" type="number" id="site_max_rooms" value="<?php echo esc_attr( get_blog_option( $id, 'site_max_rooms') ) ?>" /></td>
                        </tr>
                    </table>
                    <?php submit_button(); ?>
                </form>
            </div>
        <?php
    }
    

}

// Instantiate the class
if (is_multisite()) {
    $instance = new \StaylodgicAdmin\SiteAdmin();
}
