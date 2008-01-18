<?php
/**
 * Generate image tag with correct path
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
class USVN_View_Helper_Img {
    /**
    * @param Path to thie image without medias/TEMPLATE/images/
    * @param Alternative text (empty by default)
    */
    public function img($path, $alt = "")
    {
        $ctrl = Zend_Controller_Front::getInstance();
        return '<img src="' . $ctrl->getBaseUrl() . '/medias/default/images/' . $path . '" alt="' . $alt .'" />';
    }
}
