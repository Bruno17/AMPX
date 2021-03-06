<?php

require_once( AMP__DIR__ . '/includes/sanitizers/class-amp-base-sanitizer.php' );

/**
 * Converts relative urls of all <a> -tags to absolute urls with /amp/ - prefix
 */
class AMP_Link_Sanitizer extends AMP_Base_Sanitizer {

	public static $tag = 'a';

	public function sanitize() {
        global $modx;
        	   
        $new_tag = 'a';
	   
    	$nodes = $this->dom->getElementsByTagName( self::$tag );
		$num_nodes = $nodes->length;
        
		if ( 0 === $num_nodes ) {
			return;
		}

		for ( $i = $num_nodes - 1; $i >= 0; $i-- ) {
			$node = $nodes->item( $i );
			$attributes = AMP_DOM_Utils::get_node_attributes_as_assoc_array( $node );
            
     		if ( ! array_key_exists( 'href', $attributes ) ) {
				//$node->parentNode->removeChild( $node );
				continue;
			}
            
			$attributes = $this->filter_attributes( $attributes );
           
            $attributes['href'] = AMP_Image_Dimension_Extractor::rel2abs($attributes['href'], $modx->getOption('site_url') . 'amp/');
            
            $node->setAttribute('href',$attributes['href']);
            
			//$new_node = AMP_DOM_Utils::create_node( $this->dom, $new_tag, $new_attributes );
            
    		//$node->parentNode->replaceChild( $new_node, $node );
		}
	}

	public function get_scripts() {
		if ( ! $this->did_convert_elements ) {
			return array();
		}

		return array( self::$script_slug => self::$script_src );
	}

	private function filter_attributes( $attributes ) {
		//Todo
        // search tag_name: "A" at https://github.com/ampproject/amphtml/blob/master/validator/validator-main.protoascii
        return $attributes;
        /*
        $out = array();

		foreach ( $attributes as $name => $value ) {
			switch ( $name ) {
				case 'src':
				case 'alt':
				case 'width':
				case 'height':
				case 'class':
				case 'srcset':
				case 'sizes':
				case 'on':
					$out[ $name ] = $value;
					break;
				default;
					break;
			}
		}
        */

		return $out;
	}
    
    

}
