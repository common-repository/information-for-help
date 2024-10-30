<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
* Admin System Report Class
*
* System Report field Factory
*
* @author codeBO
* modifified by Freesoul Designstudio Team
* @project Freesoul
*/
class Eos_WH_Admin_System_Report {
	public static function output() {
		add_filter( 'load_textdomain_mofile', 'eos_wh_load_translation_file',99,2 ); //loads plugin translation files
		load_plugin_textdomain( 'wh', FALSE,EOS_WH_PLUGIN_DIR . '/languages/' );
		echo '<div class="wrap eos">';
		self::get_wp_environment_box();
		self::get_server_environment_box();
		self::get_active_plugins_box();
		self::get_theme_box();
		self::add_user_agent_box();
		self::add_debug_report_box();
		echo '</div>';
	}
	public static function get_wp_environment_box(){
		?>
        <div class="eos-widget-full top">
            <div class="eos-widget settings-box">
                <p class="eos-label" style="font-size:30px;"><?php _e( 'WordPress Environment', 'wh' ); ?></p>
                <div class="eos-list">
                    <ul>
                        <li>
                            <p><?php _e( 'Home URL', 'wh' ); ?>: <strong><?php echo home_url(); ?></strong></p>
                        </li>
                        <li>
                            <p><?php _e( 'Wordpress Version', 'wh' ); ?>: <strong><?php bloginfo( 'version' ); ?></strong></p>
                        </li>
                        <li>
                            <p><?php _e( 'Wordpress Multisite', 'wh' ); ?>: <strong><?php if ( is_multisite() ) { _e( 'Yes','wh' ); } else { _e( 'No','wh' ); } ?></strong></p>
                        </li>
                        <li>
                            <p><?php _e( 'Wordpress Debug Mode', 'wh' ); ?>: <strong><?php echo defined( 'WP_DEBUG' ) && WP_DEBUG ? __( 'Yes','wh' ) : __( 'No','wh' ); ?></strong></p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <?php
	}
	public static function get_server_environment_box() {
		global $wpdb;
		$querystr = 'SELECT table_schema AS "Database", ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS "Size (MB)" FROM information_schema.TABLES GROUP BY table_schema;';
		$dbs_info = $wpdb->get_results( $querystr );
		if( is_array( $dbs_info ) && !empty( $dbs_info ) ){
			foreach( $dbs_info as $db_info ){
				if( is_object( $db_info ) ){
					if( DB_NAME === $db_info->Database ){
						$vars = get_object_vars( $db_info );
						if( NULL !== $vars ){
							foreach( $vars as $k => $v ){
								$db_size = false !== strpos( '_'.$k,'Size' ) ? $v : false;
							}
						}
					}
				}
			}
		}
		$host_name = function_exists( 'gethostname' ) ? gethostname() : false;
		?>
        <div class="eos-widget-full top">
            <div class="eos-widget settings-box">
                <p class="eos-label" style="font-size:30px;"><?php _e( 'Server Environment', 'wh' ); ?></p>
                <div class="eos-list">
                    <ul>
						<?php if( $host_name ){
							$hostA = explode( '.',$host_name );
							$host_url = count( $hostA ) > 1 ? esc_url( $hostA[count( $hostA ) - 2].'.'.$hostA[count( $hostA ) - 1] ) : '#';
						?>
                        <li>
                            <p><?php _e( 'Host', 'freesoul' ); ?>: <a href="<?php echo $host_url; ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $host_name ); ?></a></p>
                        </li>
						<?php } ?>					
						<li>
                            <p><?php _e( 'PHP Version', 'wh' ); ?>: <strong><?php
									// Check if phpversion function exists.
							if ( function_exists( 'phpversion' ) ) {
								$php_version = phpversion();
								if ( version_compare( $php_version, '5.3', '<' ) ) {
									echo '<mark class="error">' . sprintf( __( '%s - We recommend a minimum PHP version of 5.3.', 'wh' ), esc_html( $php_version ) ) . '</mark>';
								} else {
									echo '<mark class="yes">' . esc_html( $php_version ) . '</mark>';
								}
							} else {
								_e( "Couldn't determine PHP version because phpversion() doesn't exist.", 'wh' );
							}
									?></strong></p>
                        </li>
                        <li>
                            <p><?php _e( 'MySQL Version', 'wh' ); ?>: <strong><?php
							/** @global wpdb $wpdb */
							global $wpdb;
							echo $wpdb->db_version();
							?></strong></p>
                        </li>
						<?php if( $db_size ){ ?>
						<li>
                            <p><?php _e( 'Database size', 'wh' ); ?>: <strong><?php
							echo esc_attr( $db_size ).' Mb';
							?></strong></p>
                        </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php
	}
	public static function get_active_plugins_box() {
		$mu_plugins = wp_get_mu_plugins();
		$active_plugins = (array) get_option( 'active_plugins', array() );
		?>
		<div class="eos-widget-full top">
			<div class="eos-widget settings-box">
				<p class="eos-label" style="font-size:30px;"><?php _e( 'Must Use Plugins', 'wh' ); ?></p>
				<div class="eos-list">
					<ul>
					<?php
					foreach( $mu_plugins as $mu_plugin ) {
						?>
						<li><?php echo basename( $mu_plugin ); ?></li>
					<?php } ?>
					</ul>
                </div>
            </div>
        </div>
		<div class="eos-widget-full top">
			<div class="eos-widget settings-box">
				<p class="eos-label" style="font-size:30px;"><?php _e( 'Active Plugins', 'wh' ); ?></p>
				<div class="eos-list">
					<ul>
					<?php
					$n = 0;
					$plugsN = count( $active_plugins );
					foreach( $active_plugins as $plugin ) {
						$plugin_data = @get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
						$version_string = '';
						if ( ! empty( $plugin_data['Name'] ) ) {
							$plugin_name = esc_html( $plugin_data['Name'] );
						}
						if ( ! empty( $version_data['version'] ) && version_compare( $version_data['version'], $plugin_data['Version'], '>' ) ) {
							$version_string = ' &ndash; <strong style="color:red;">' . esc_html( sprintf( _x( '%s is available', 'Version info', 'wh' ), $version_data['version'] ) ) . '</strong>';
						}
						$suffix = $n + 1 < $plugsN ? '; ' : '';
						?>
						<li><?php echo $plugin_name.' '.esc_html( $plugin_data['Version'] ) . $version_string . $suffix; ?></li>
						<?php
						++$n;
					}
					?>
					</ul>
                </div>
            </div>
        </div>
		<?php
	}
	public static function get_theme_box() {
		include_once( ABSPATH . 'wp-admin/includes/theme-install.php' );
		$active_theme = wp_get_theme();
		// @codingStandardsIgnoreStart
		$theme_version = $active_theme->Version;
		$theme_template = $active_theme->Template;
		// @codingStandardsIgnoreEnd
		?>
        <div class="eos-widget-full top">
            <div class="eos-widget settings-box">
                <p class="eos-label" style="font-size:30px;"><?php _e( 'Current Theme', 'wh' ); ?></p>
                <div class="eos-list">
                    <ul>
                        <li>
                            <p><?php _e( 'Theme', 'wh' ); ?>: <strong><?php echo $active_theme; ?></strong></p>
                        </li>
                        <li>
                            <p><?php _e( 'Theme Version', 'wh' ); ?>: <strong><?php echo $theme_version; ?></strong></p>
                        </li>
                        <li>
                            <p><?php _e( 'Child Theme', 'wh' ); ?>: <strong><?php
									echo is_child_theme() ? '<mark class="yes">'.__( 'Yes','wh' ).'</mark>' : __( 'No','wh' ); ?></strong></p>
                        </li>
                        <?php
						if ( is_child_theme() ) :
							$parent_theme = wp_get_theme( $theme_template );
							?>
                            <li>
                                <p><?php _e( 'Parent Theme', 'wh' ); ?>: <strong><?php echo $parent_theme; ?></strong></p>
                            </li>
						<?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php
	}
	public static function add_user_agent_box() {
	?>
        <div class="eos-widget-full top">
            <div class="eos-widget settings-box">
                <p class="eos-label" style="font-size:30px;"><?php _e( 'User Agent', 'wh' ); ?></p>
                <div class="eos-list">
                    <ul>
                        <li>
                            <p><strong id="eos-user-agent"></strong></p>
                        </li>
					</ul>
				</div>
			</div>
		</div>
	<?php
	}
	public static function add_debug_report_box() {
		?>
        <div class="eos-widget-full top">
            <div class="eos-widget">
                <p class="eos-label" style="font-size:30px;"><?php _e( 'Copy System Report for Support', 'wh' ); ?></p>
                <p class="eos-description">
                    <div id="eos-debug-report">
                        <textarea style="width:100%" rows="20" readonly="readonly"></textarea>
                        <p class="submit"><button id="copy-for-support" class="button-primary" href="#" ><?php _e( 'Copy for Support', 'wh' ); ?></button></p>
                    </div>
                </p>
            </div>
        </div>
        <?php
	}
}
