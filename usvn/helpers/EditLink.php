<?php
/**
 * Generate edit link
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package helper
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */
class USVN_View_Helper_EditLink {
    /**
     * Generates edit link
     *
     * @access public
     *
     * @param string Param name of ressource to delete (ex: login or name)
     * @param string Name of ressource
     *
     * @return string HTML link: <a href="test">Test</a>.
     */
    public function editLink($param, $name)
    {
        $front = Zend_Controller_Front::getInstance();
        $view = Zend_Controller_Action_HelperBroker::getExistingHelper('viewRenderer')->view;
        $url = $view->url(array('action' => 'edit', $param => $name));
        $img = $view->img("CrystalClear/16x16/actions/edit.png", T_("Edit"));
        return <<< EOF
        <a href="{$url}">
            {$img}
        </a>
EOF;
    }
}
