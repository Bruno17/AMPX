<?php
/**
 * AMPX
 *
 * Copyright 2016 by Bruno Perner <b.perner@gmx.de>
 *
 * @package ampx
 * @subpackage plugin
 *
 * ampx plugin.
 */

switch ($modx->event->name) {

    case 'OnPageNotFound':

        /* handle redirects */
        $search = $_SERVER['REQUEST_URI'];
        $base_url = $modx->getOption('base_url');
        if ($base_url != '/') {
            $search = str_replace($base_url, '', $search);
        }

        $params = explode('?', $search);
        $search = isset($params[0]) ? $params[0] : '';
        $params = isset($params[1]) ? $params[1] : '';

        $parts = explode('/', $search);

        if (isset($parts[1]) && $parts[1] == 'amp') {
            $url = str_replace('/amp/', '', $search);
            if ($id = $modx->findResource($url)) {
                if ($resource = $modx->getObject('modResource', $id)) {
                    if ($template = $modx->getObject('modTemplate', $resource->get('template'))) {
                        $templatename = $template->get('templatename');
                        //is there an AMP - template (same templatename with _AMP suffix)?
                        if ($amptemplate = $modx->getObject('modTemplate', array('templatename' => $templatename . '_AMP'))) {
                            //for SwitchTemplate
                            $_REQUEST['mode'] = $_GET['mode'] = 'amp_news';
                            $modx->setPlaceholder('+amp_site_url', $modx->getOption('site_url') . 'amp/');
                            $modx->is_amp = 'amp';
                            //echo $_SERVER['REQUEST_URI'] . '<br>';
                            //echo $id;
                            //echo 'amp';
                            $modx->resourceIdentifier = $id;
                            $modx->resourceMethod = 'id';
                        }
                        //send forward without template-switch
                        $modx->sendForward($id);
                    }
                }
            } else {
                $modx->sendRedirect($url);
            }
        }

        break;

    case 'OnSwitchTemplateParsed':

        $ampxCorePath = realpath($modx->getOption('ampx.core_path', null, $modx->getOption('core_path') . 'components/ampx'));

        if (!defined('AMP__DIR__')) {
            DEFINE('AMP__DIR__', $ampxCorePath);
        }

        $ampxCorePath .= '/';

        $ampx = $modx->getService('ampx', 'Ampx', $ampxCorePath . 'model/ampx/');

        //$mode = $_GET['mode'];
        if ($mode == 'amp_news') {
            $ampx->sanitize($modx->eventoutput);
        }
        break;
}

return '';