<?php

class AMP_DOM_Utils {
    public static function get_dom_from_content($content) {
        $libxml_previous_state = libxml_use_internal_errors(true);


        // Create a DOM object
        $html = new simple_html_dom();

        // Load HTML from a string
        $html->load($content);
        //$body = $html->find('div[id=main-container]', 0);
        if ($body){
            $body = $body->innertext;        
        }else{
            $body = $html->find('body', 0)->innertext;    
        }
                         
        $dom = new DOMDocument();
        // Wrap in dummy tags, since XML needs one parent node.
        // It also makes it easier to loop through nodes.
        // We can later use this to extract our nodes.
        // Add utf-8 charset so loadHTML does not have problems parsing it. See: http://php.net/manual/en/domdocument.loadhtml.php#78243
        $result = $dom->loadHTML('<html><head><meta http-equiv="content-type" content="text/html; charset=utf-8"></head><body>' . $body . '</body></html>');

        //$result = $dom->loadHTML($content);

        libxml_clear_errors();
        libxml_use_internal_errors($libxml_previous_state);

        if (!$result) {
            return false;
        }

        return $dom;
    }

    public static function get_content_from_dom($dom,$content) {
        // Only want children of the body tag, since we have a subset of HTML.
        /*
        $out = '';
        $body = $dom->getElementsByTagName( 'body' )->item( 0 );
        foreach ( $body->childNodes as $node ) {
        $out .= $dom->saveXML( $node, LIBXML_NOEMPTYTAG );
        }
        */
        $out = $dom->saveHTML();
        
        // Create a DOM object
        $html = new simple_html_dom();

        // Load HTML from a string
        $html->load($out);
        $body = $html->find('body', 0)->innertext;                
                
                // Create a DOM object
        $html = new simple_html_dom();

        // Load HTML from a string
        $html->load($content);
        //$contentbody = $html->find('div[id=main-container]', 0);
        if ($contentbody){
            $contentbody->innertext = $body;   
        }else{
            $html->find('body', 0)->innertext = $body;   
        }        
        $out = $html;

        return $out;
    }

    public static function create_node($dom, $tag, $attributes) {
        $node = $dom->createElement($tag);
        self::add_attributes_to_node($node, $attributes);
        return $node;
    }

    public static function get_node_attributes_as_assoc_array($node) {
        $attributes = array();
        foreach ($node->attributes as $attribute) {
            $attributes[$attribute->nodeName] = $attribute->nodeValue;
        }
        return $attributes;
    }

    public static function add_attributes_to_node($node, $attributes) {
        foreach ($attributes as $name => $value) {
            $node->setAttribute($name, $value);
        }
    }

    public static function is_node_empty($node) {
        return 0 === $node->childNodes->length && empty($node->textContent);
    }
}
