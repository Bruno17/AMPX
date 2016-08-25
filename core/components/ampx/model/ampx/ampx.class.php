<?php

/**
 * AMPX
 *
 * Copyright 2016 by Bruno Perner <b.perner@gmx.de>
 *
 * @package ampx
 * @subpackage classfile
 */

function absint( $maybeint ) {
    return abs( intval( $maybeint ) );
}

class Ampx {
    /**
     * A reference to the modX instance
     * @var modX $modx
     */
    public $modx;

    /**
     * The namespace
     * @var string $namespace
     */
    public $namespace = 'ampx';

    /**
     * The class options
     * @var array $options
     */
    public $options = array();

    /**
     * A configuration array
     * @var boolean $fromCache
     */
    public $fromCache;

    /**
     * Ampx constructor
     *
     * @param modX $modx A reference to the modX instance.
     * @param array $options An array of options. Optional.
     */
    function __construct(modX & $modx, array $options = array()) {
        $this->modx = &$modx;

        $this->modx->lexicon->load('ampx:default');

        $corePath = $this->getOption('core_path', $options, $this->modx->getOption('core_path') . 'components/ampx/');
        $assetsPath = $this->getOption('assets_path', $options, $this->modx->getOption('assets_path') . 'components/ampx/');
        $assetsUrl = $this->getOption('assets_url', $options, $this->modx->getOption('assets_url') . 'components/ampx/');

        $this->fromCache = false;

        // Load some default paths for easier management
        $this->options = array_merge(array(
            'namespace' => $this->namespace,
            'assetsPath' => $assetsPath,
            'assetsUrl' => $assetsUrl,
            'cssUrl' => $assetsUrl . 'css/',
            'jsUrl' => $assetsUrl . 'js/',
            'imagesUrl' => $assetsUrl . 'images/',
            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'chunksPath' => $corePath . 'elements/chunks/',
            'pagesPath' => $corePath . 'elements/pages/',
            'snippetsPath' => $corePath . 'elements/snippets/',
            'pluginsPath' => $corePath . 'elements/plugins/',
            'processorsPath' => $corePath . 'processors/',
            'templatesPath' => $corePath . 'templates/',
            'cachePath' => $this->modx->getOption('core_path') . 'cache/',
            'connectorUrl' => $assetsUrl . 'connector.php'), $options);

        // Load parameters
        $this->options = array_merge($this->options, array(
            'mode_key' => $this->getOption('mode_key', $options, 'mode', true),
            'cache_resource_key' => $this->getOption('cache_resource_key', $options, $this->modx->getOption(xPDO::OPT_CACHE_KEY, null, 'resource'), true),
            'cache_resource_handler' => $this->getOption('cache_resource_handler', $options, $this->modx->getOption(xPDO::OPT_CACHE_HANDLER, null, 'xPDOFileCache'), true),
            'cache_resource_expires' => (integer)$this->getOption('cache_resource_expires', $options, $this->modx->getOption(xPDO::OPT_CACHE_EXPIRES, null, 0), true),
            'max_iterations' => (integer)$this->modx->getOption('parser_max_iterations', null, 10)));

        //$this->modx->addPackage('switchtemplate', $this->getOption('modelPath'));
        $this->modx->lexicon->load('ampx:default');
    }

    /**
     * Get a local configuration option or a namespaced system setting by key.
     *
     * @param string $key The option key to search for.
     * @param array $options An array of options that override local options.
     * @param mixed $default The default value returned, if the option is not found locally or as a namespaced system setting.
     * @param bool $skipEmpty If true: use default value if option value is empty.
     * @return mixed The option value or the default value specified.
     */
    public function getOption($key, $options = array(), $default = null, $skipEmpty = false) {
        $option = $default;
        if (!empty($key) && is_string($key)) {
            if ($options !== null && array_key_exists($key, $options) && !($skipEmpty && empty($options[$key]))) {
                $option = $options[$key];
            } elseif (array_key_exists($key, $this->options) && !($skipEmpty && empty($options[$key]))) {
                $option = $this->options[$key];
            } elseif (array_key_exists("{$this->namespace}.{$key}", $this->modx->config)) {
                $option = $this->modx->getOption("{$this->namespace}.{$key}", null, $default, $skipEmpty);
            }
        }
        return $option;
    }

    public function runSanitizer($sanitizer_class,& $dom, $args = array()) {
        $filename = 'class-' . strtolower(str_replace('_','-',$sanitizer_class)) . '.php';
        require_once (AMP__DIR__ . '/includes/sanitizers/' . $filename);
        
        $sanitizer = new $sanitizer_class($dom, $args);
        if (!is_subclass_of($sanitizer, 'AMP_Base_Sanitizer')) {
            //_doing_it_wrong( __METHOD__, sprintf( __( 'Sanitizer (%s) must extend `AMP_Base_Sanitizer`', 'amp' ), esc_html( $sanitizer_class ) ), '0.1' );
            
            return;
        }
        $sanitizer->sanitize();
        //$this->add_scripts( $sanitizer->get_scripts() );
        
        return true;
    }


    public function sanitize(&$output) {
        //$output = str_replace('<img ',' <img-amp', $output);

        require_once (AMP__DIR__ . '/includes/utils/class-amp-dom-utils.php');
        require_once (AMP__DIR__ . '/includes/sanitizers/class-amp-base-sanitizer.php');
        require_once (AMP__DIR__ . '/includes/embeds/class-amp-base-embed-handler.php');

        //require_once (AMP__DIR__ . '/includes/class-amp-content.php');

        //require_once (AMP__DIR__ . '/includes/sanitizers/class-amp-blacklist-sanitizer.php');
        //require_once (AMP__DIR__ . '/includes/sanitizers/class-amp-img-sanitizer.php');
        //require_once (AMP__DIR__ . '/includes/sanitizers/class-amp-video-sanitizer.php');
        //require_once (AMP__DIR__ . '/includes/sanitizers/class-amp-iframe-sanitizer.php');
        //require_once (AMP__DIR__ . '/includes/sanitizers/class-amp-audio-sanitizer.php');

        require_once (AMP__DIR__ . '/includes/embeds/class-amp-twitter-embed.php');
        require_once (AMP__DIR__ . '/includes/embeds/class-amp-youtube-embed.php');
        require_once (AMP__DIR__ . '/includes/embeds/class-amp-gallery-embed.php');
        require_once (AMP__DIR__ . '/includes/embeds/class-amp-instagram-embed.php');
        require_once (AMP__DIR__ . '/includes/embeds/class-amp-vine-embed.php');
        require_once (AMP__DIR__ . '/includes/embeds/class-amp-facebook-embed.php');

        $dom = AMP_DOM_Utils::get_dom_from_content($output);
        
        $this->runSanitizer('AMP_Img_Sanitizer',$dom);
        $this->runSanitizer('AMP_Link_Sanitizer',$dom);
        $this->runSanitizer('AMP_Form_Sanitizer',$dom);
       
        $output = AMP_DOM_Utils::get_content_from_dom($dom);
        

        return true;
    }
}
