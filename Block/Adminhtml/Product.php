<?php
/**
 * Magepow 
 * @category    Magepow 
 * @copyright   Copyright (c) 2014 Magepow (http://www.magepow.com/) 
 * @license     http://www.magepow.com/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-01-05 10:40:51
 * @@Modify Date: 2018-03-21 12:33:10
 * @@Function:
 */

namespace Magepow\MultiTranslate\Block\Adminhtml;

class Product extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor.
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_product';
        $this->_blockGroup = 'Magepow_MultiTranslate';
        $this->_headerText = __('Product');
        parent::_construct();
        $this->removeButton('add');
    }
}
