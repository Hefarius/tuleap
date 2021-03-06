<?php

/**
* Copyright (c) Xerox Corporation, Codendi Team, 2001-2009. All rights reserved
* 
* 
*
* Docman_View_Admin
*/

require_once('Docman_View_Extra.class.php');

class Docman_View_Admin extends Docman_View_Extra {
    
    function _title($params) {
        echo '<h2>'. $GLOBALS['Language']->getText('plugin_docman', 'service_lbl_key') .' - '. $GLOBALS['Language']->getText('plugin_docman', 'admin_title') .'</h2>';
    }
    function _content($params) {
        $html = '';
        $html .= '<h3><a href="'. $this->buildUrl($params['default_url'], array('action' => 'admin_permissions')) .'">'. $GLOBALS['Language']->getText('plugin_docman', 'admin_permissions_title') .'</a></h3>';
        $html .= '<p>'. $GLOBALS['Language']->getText('plugin_docman', 'admin_permissions_descr') .'</p>';
        
        $html .= '<h3><a href="'. $this->buildUrl($params['default_url'], array('action' => 'admin_view')) .'">'. $GLOBALS['Language']->getText('plugin_docman', 'admin_view_title') .'</a></h3>';
        $html .= '<p>'. $GLOBALS['Language']->getText('plugin_docman', 'admin_view_descr') .'</p>';

        $html .= '<h3><a href="'. $this->buildUrl($params['default_url'], array('action' => 'admin_metadata')) .'">'. $GLOBALS['Language']->getText('plugin_docman', 'admin_metadata_title') .'</a></h3>';
        $html .= '<p>'. $GLOBALS['Language']->getText('plugin_docman', 'admin_metadata_descr') .'</p>';

        $html .= '<h3><a href="'. $this->buildUrl($params['default_url'], array('action' => 'report_settings')) .'">'. $GLOBALS['Language']->getText('plugin_docman', 'admin_report_title') .'</a></h3>';
        $html .= '<p>'. $GLOBALS['Language']->getText('plugin_docman', 'admin_report_descr') .'</p>';

        $html .= '<h3><a href="'. $this->buildUrl($params['default_url'], array('action' => 'admin_obsolete')) .'">'. $GLOBALS['Language']->getText('plugin_docman', 'admin_obsolete_title') .'</a></h3>';
        $html .= '<p>'. $GLOBALS['Language']->getText('plugin_docman', 'admin_obsolete_descr') .'</p>';

        $html .= '<h3><a href="'. $this->buildUrl($params['default_url'], array('action' => 'admin_lock_infos')) .'">'. $GLOBALS['Language']->getText('plugin_docman', 'admin_lock_infos_title'). '</a></h3>';
        $html .= '<p>'. $GLOBALS['Language']->getText('plugin_docman', 'admin_lock_infos_descr') .'</p>';

        echo $html;
    }
}

?>
