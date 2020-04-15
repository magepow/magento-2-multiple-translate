<?php
/**
 * Magepow 
 * @category    Magepow 
 * @copyright   Copyright (c) 2014 Magepow (http://www.magepow.com/) 
 * @license     http://www.magepow.com/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-01-05 10:40:51
 * @@Modify Date: 2018-03-21 20:50:14
 * @@Function:
 */

namespace Magepow\MultiTranslate\Block\Adminhtml\Product;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
 
    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }
    
    /**
     * _construct
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'product_id';
        $this->_blockGroup = 'Magepow_MultiTranslate';
        $this->_controller = 'adminhtml_product';

        parent::_construct();

        $this->buttonList->update('delete', 'label', __('Delete'));
        $this->buttonList->remove('delete');

        if ($this->_isAllowedAction('MultiTranslate_Product::save')) {
            $this->buttonList->update('save', 'label', __('Save Product'));
            // $this->buttonList->add(
            //     'saveandcontinue',
            //     [
            //         'label' => __('Save and Continue Edit'),
            //         'class' => 'save',
            //         'data_attribute' => [
            //             'mage-init' => [
            //                 'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
            //             ],
            //         ]
            //     ],
            //     -100
            // );
        } else {
            $this->buttonList->remove('save');
        }
 
        // if ($this->_coreRegistry->registry('product')->getId()) {
            $this->buttonList->remove('reset');
        // }
    }

    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    /**
     * Retrieve the save and continue edit Url.
     *
     * @return string
     */
    protected function getSaveAndContinueUrl()
    {
        return $this->getUrl(
            '*/*/save',
            [
                '_current' => true,
                'back' => 'edit',
                'tab' => '{{tab_id}}',
                'store' => $this->getRequest()->getParam('store'),
                'product_id' => $this->getRequest()->getParam('product_id'),
                'current_product_id' => $this->getRequest()->getParam('current_product_id'),
            ]
        );
    }

    /**
     * Retrieve the save and continue edit Url.
     *
     * @return string
     */
    protected function getSaveAndCloseWindowUrl()
    {
        return $this->getUrl(
            '*/*/save',
            [
                '_current' => true,
                'back' => 'edit',
                'tab' => '{{tab_id}}',
                'store' => $this->getRequest()->getParam('store'),
                'product_id' => $this->getRequest()->getParam('product_id'),
                'current_product_id' => $this->getRequest()->getParam('current_product_id'),
                'saveandclose' => 1,
            ]
        );
    }
}
