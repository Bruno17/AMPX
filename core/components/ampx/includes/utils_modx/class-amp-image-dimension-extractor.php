<?php

class AMP_Image_Dimension_Extractor {
	static $callbacks_registered = false;

	static public function extract( $url ) {
	   
        /*
		if ( ! self::$callbacks_registered ) {
			self::register_callbacks();
		}
        */
        
 		$url = self::normalize_url( $url );
		if ( false === $url ) {
			return false;
		}

		//return apply_filters( 'amp_extract_image_dimensions', false, $url );
        return self::extract_by_downloading_image( false, $url );
	}
    
    public static function rel2abs($rel, $base) {

        // parse base URL  and convert to local variables: $scheme, $host,  $path
        extract(parse_url($base));

        if (strpos($rel, "//") === 0) {
            return $scheme . ':' . $rel;
        }

        // return if already absolute URL
        if (parse_url($rel, PHP_URL_SCHEME) != '') {
            return $rel;
        }

        // queries and anchors
        if ($rel[0] == '#' || $rel[0] == '?') {
            return $base . $rel;
        }

        // remove non-directory element from path
        $path = preg_replace('#/[^/]*$#', '', $path);

        // destroy path if relative url points to root
        /*
        if ($rel[0] == '/') {
            $path = '';
        }
        */

        // dirty absolute URL
        $abs = $host . $path . "/" . $rel;

        // replace '//' or  '/./' or '/foo/../' with '/'
        $abs = preg_replace("/(\/\.?\/)/", "/", $abs);
        $abs = preg_replace("/\/(?!\.\.)[^\/]+\/\.\.\//", "/", $abs);

        // absolute URL is ready!
        return $scheme . '://' . $abs;
    }    

	public static function normalize_url( $url ) {
	   
		if ( empty( $url ) ) {
			return false;
		}
        
		if ( 0 === strpos( $url, 'data:' ) ) {
			return false;
		}
        /*
		if ( 0 === strpos( $url, '//' ) ) {
			return set_url_scheme( $url, 'http' );
		}
        */

		$parsed = parse_url( $url );
		if ( ! isset( $parsed['host'] ) ) {
			$path = '';
			if ( isset( $parsed['path'] ) ) {
				$path .= $parsed['path'];
			}
			if ( isset( $parsed['query'] ) ) {
				$path .= '?' . $parsed['query'];
			}
 			//$url = site_url( $path );
            $url = MODX_SITE_URL . $path;
		}

		return $url;
	}

	public static function extract_by_downloading_image( $dimensions, $url ) {
	    if (empty($url)){
	       return false;
	    }
       
       
		if ( is_array( $dimensions ) ) {
			return $dimensions;
		}
        /*
		$url_hash = md5( $url );
		$transient_name = sprintf( 'amp_img_%s', $url_hash );
		$transient_expiry = 30 * DAY_IN_SECONDS;
		$transient_fail = 'fail';

		$dimensions = get_transient( $transient_name );

		if ( is_array( $dimensions ) ) {
			return $dimensions;
		} elseif ( $transient_fail === $dimensions ) {
			return false;
		}

		// Very simple lock to prevent stampedes
		$transient_lock_name = sprintf( 'amp_lock_%s', $url_hash );
		if ( false !== get_transient( $transient_lock_name ) ) {
			return false;
		}
		set_transient( $transient_lock_name, 1, MINUTE_IN_SECONDS );
        */
		// Note to other developers: please don't use this class directly as it may not stick around forever...
		if ( ! class_exists( 'FastImage' ) ) {
			require_once( AMP__DIR__ . '/includes/lib/class-fastimage.php' );
		}

		// TODO: look into using curl+stream (https://github.com/willwashburn/FasterImage)
		$image = new FastImage( $url );
		$dimensions = $image->getSize();

		if ( ! is_array( $dimensions ) ) {
		    echo $url;die();
			//set_transient( $transient_name, $transient_fail, $transient_expiry );
			//delete_transient( $transient_lock_name );
			return false;
		}

		//set_transient( $transient_name, $dimensions, $transient_expiry );
		//delete_transient( $transient_lock_name );
		return $dimensions;
	}
}
