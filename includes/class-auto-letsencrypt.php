<?php
/**
 * Main class of the plugin interacting with the WordPress.
 *
 * @package Auto_Letsencrypt
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Auto_Letsencrypt' ) ) {

	/**
	 * Main class of the plugin interacting with the WordPress.
	 *
	 * @since 1.0.0
	 */
	class Auto_Letsencrypt {

		/**
		 * The instance of the class Auto_Letsencrypt.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @static
		 *
		 * @var Auto_Letsencrypt
		 */
		protected static $instance = null;

		/**
		 * Register hooks and require needed files.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return void
		 */
		public function __construct() {
			add_action( 'wpmu_new_blog', array( $this, 'sync_certificates' ) );
			add_action( 'refresh_blog_details', array( $this, 'sync_certificates' ) );
		} // End __construct.

		/**
		 * Syncs the certificates depending upon the domains of the sites
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return void
		 */
		public function sync_certificates() {
			if ( ! is_callable( 'exec' ) ) {
				// Return early if we can't even call exec.
				return;
			}

			// We don't want to include archived, spammed or deleted sites for SSL.
			$sites_args = array(
				'archived'   => 0,
				'spam'       => 0,
				'deleted'    => 0,
			);

			$sites   = get_sites( $sites_args );
			$domains = array_unique( array_map( array( $this, 'get_site_domain' ), $sites ) );

			$args   = array();

			// The main command.
			$args[] = 'sudo certbot';

			// Subcommand to issue only certificate.
			$args[] = 'certonly';

			// Authentication via webroot.
			$args[] = '--webroot';

			// If domains are added or removed, lets expand.
			$args[] = '--expand';

			// Allowing to generate certificate even if some domains don't validate.
			$args[] = '--allow-subset-of-names';

			// Only review if new domains are added.
			$args[] = '--renew-with-new-domains';

			// Don't renew if certificate is not expiring.
			$args[] = '--keep-until-expiring';

			// Lets quiet the output.
			$args[] = '-q';

			// Webroot path.
			$args[] = '-w ' . ABSPATH;

			// Forcing certificates to be in the same folder instead of new.
			$args[] = '--cert-name ' . $domains[0];

			// Lets add all domains/subdomains.
			foreach ( $domains as $domain ) {
				$args[] = '-d ' . $domain;
			}

			// Creating command to execute.
			$command = implode( ' ', $args );

			$status  = null;
			$output  = '';

			// Hopefully new certificate will be created with this command.
			exec( $command, $output, $status );

			if ( 0 === $status ) {

				// Lets see if the nginx has found any errors in config.
				exec( 'sudo nginx -t', $output, $status );
				if ( 0 === $status ) {

					// Everything working, lets reload nginx to use new certificate.
					exec( 'sudo service nginx reload', $output, $status );
				}
				// @todo Add conditions if nginx -t fails.
			}
			// @todo Figure out what to do when can't create certificate.
		} // End sync_certificates.

		/**
		 * Returns domain from the WP_Site object
		 *
		 * @since 1.0.0
		 * @access private
		 *
		 * @param WP_Site $site WP_Site object.
		 *
		 * @return string Domain name from the give WP_Site object.
		 */
		private function get_site_domain( $site ) {
			return $site->domain;
		}

		/**
		 * Returns the current instance of the class.
		 *
		 * @since 1.0.0
		 * @access public
		 * @static
		 *
		 * @return Auto_Letsencrypt Returns the current instance of the class.
		 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

	} // End class Auto_Letsencrypt.

	Auto_Letsencrypt::get_instance();

}
