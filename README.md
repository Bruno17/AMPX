# AMPX
Accelerated Mobile Pages (AMP) for MODX Revolution

## Requirements
* The SwitchTemplate - plugin by Jako

## Features
* Call AMP pages with the "/amp/" - prefix at the page URI 
* AMPX switches to an AMP - Template, if there is a Template with the same templatename + _AMP
* Sanitizes and converts the HTML of the body to AMP HTML
* Rewrites all relative links to absolute links with the "/amp/" - prefix
* If there isn't a AMP - Template it laods the page with the normal template without switching to the AMP HTML
* At normal pages with "/amp/" - prefix, AMPX converts all relative links to absolute links with "/amp/" - prefix
* Add a canonical-tag with the URL of the normal page to pages, which was called with the "/amp/" - prefix

