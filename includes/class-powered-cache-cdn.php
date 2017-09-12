<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Powered_Cache_CDN {

	/**
	 * Return an instance of the current class
	 *
	 * @since 1.0
	 * @return Powered_Cache_CDN
	 */
	public static function factory() {
		static $instance = false;

		if ( ! $instance ) {
			$instance = new self();
			$instance->setup();
		}

		return $instance;
	}

	/**
	 * Setup hooks
	 *
	 * @since 1.0
	 */
	public function setup() {

		add_filter( 'wp_get_attachment_url', array( $this, 'cdn_url' ), 9999 );
		add_filter( 'stylesheet_uri', array( $this, 'cdn_url' ), 9999 );
		add_filter( 'smilies_src', array( $this, 'cdn_url' ), 9999 );
		add_filter( 'bp_core_fetch_avatar_url', array( $this, 'cdn_url' ), 9999 );
		add_filter( 'style_loader_src', array( $this, 'cdn_url' ), 9999 );
		add_filter( 'script_loader_src', array( $this, 'cdn_url' ), 9999 );


		add_filter( 'the_content', array( $this, 'cdn_images' ), 9999 );
		add_filter( 'post_thumbnail_html', array( $this, 'cdn_images' ), 9999 );
		add_filter( 'get_avatar', array( $this, 'cdn_images' ), 9999 );
		add_filter( 'bp_core_fetch_avatar', array( $this, 'cdn_images' ), 9999 );
		add_filter( 'widget_text', array( $this, 'cdn_images' ), 9999 );


		do_action( 'powered_cache_cdn_setup' );
	}


	public function cdn_url( $url ) {
		if ( is_admin() || is_preview() ) {
			return $url;
		}

		return $this->maybe_cdn_replace( $url );
	}



	public function cdn_images( $content ) {
		if ( is_admin() || is_preview() || empty( $content ) ) {
			return $content;
		}

		if ( ! class_exists( 'DOMDocument' ) ) {
			return $content;
		}

		$document = new DOMDocument();
		@$document->loadHTML($content);
		$images = $document->getElementsByTagName('img');

		$img_url = array();
		foreach ( $images as $img ) {
			if ( $img->hasAttribute( 'src' ) ) {
				$img_url[] = $img->getAttribute( 'src' );
			}

			if ( $img->hasAttribute( 'srcset' ) ) {
				$imgset = explode( ',', $img->getAttribute( 'srcset' ) );
				foreach ( $imgset as $src_item ) {
					$imgsrc    = explode( ' ', trim( $src_item ) );
					// first item is url, second width
					$img_url[] = $imgsrc[0];
				}
			}
		}

		$img_url = array_unique($img_url);
		$cdn_url = array();

		foreach ( $img_url as $replace_url ) {
			$cdn_url[] = $this->maybe_cdn_replace( $replace_url );
		}


		return str_replace($img_url,$cdn_url,$content);
	}


	/**
	 * this method decide to url replacement.
	 * rejected files & external resources will be ignored
	 *
	 * @since 1.0
	 *
	 * @param string $url
	 * @return string cdn url
	 */
	public static function maybe_cdn_replace( $url ) {
		global $powered_cache_options;

		$raw_url = $url;

		// rejected file
		if ( ! empty( $powered_cache_options['cdn_rejected_files'] ) ) {
			$cdn_rejected_files = preg_split( '#(\r\n|\r|\n)#', $powered_cache_options['cdn_rejected_files'] );
			$cdn_rejected_files = implode( '|', $cdn_rejected_files );

			if ( preg_match( '#(' . $cdn_rejected_files . ')#', $url ) ) {
				return $url;
			}
		}

		// external resource
		if ( false === strpos( $url, site_url() ) ) {
			return $url;
		}

		// clean version string
		if ( strpos( $url, '?ver=' ) ) {
			$raw_url = remove_query_arg( 'ver', $url );
		}

		$raw_url = untrailingslashit( $raw_url );
		$ext = explode( '.', $raw_url );
		$ext = strtolower( end( $ext ) );

		$zone = 'all';
		$image_extensions = apply_filters( 'powered_cache_cdn_image_extensions', array( 'jpg', 'jpeg', 'gif', 'png', 'bmp', 'ico' ) );

		if ( in_array( $ext, $image_extensions ) ) {
			$zone = 'image';
		} elseif ( 'css' == $ext ) {
			$zone = 'css';
		} elseif ( 'js' == $ext ) {
			$zone = 'js';
		}

		$cdn_url = self::get_best_possible_cdn_host( $zone );

		// no host found
		if ( false === $cdn_url ) {
			return $url;
		}

		return str_replace( site_url(), $cdn_url, $url );
	}

	/**
	 * try to catch best cdn address to given zone
	 * @param string $zone keys of Powered_Cache_Admin_Helper::cdn_zones
	 *
	 * @return mixed  string | false
	 */
	public static function get_best_possible_cdn_host( $zone = 'all' ) {
		global $powered_cache_cdn_addresses;

		if ( ! isset( $powered_cache_cdn_addresses ) ) {
			$powered_cache_cdn_addresses = powered_cache_cdn_addresses();
		}

		if ( isset( $powered_cache_cdn_addresses[ $zone ] ) && is_array( $powered_cache_cdn_addresses[ $zone ] ) ) {
			// if we have multiple host for the same resource get randomly
			$random_key = array_rand( $powered_cache_cdn_addresses[ $zone ] );

			return $powered_cache_cdn_addresses[ $zone ][ $random_key ];
		}

		// fallback to primary host
		if ( isset( $powered_cache_cdn_addresses['all'] ) && is_array( $powered_cache_cdn_addresses['all'] ) ) {
			$random_key = array_rand( $powered_cache_cdn_addresses['all'] );

			return $powered_cache_cdn_addresses['all'][ $random_key ];
		}

		return false;
	}

}

