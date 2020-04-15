<?php
/**
 * Magepow 
 * @category    Magepow 
 * @copyright   Copyright (c) 2014 Magepow (http://www.magepow.com/) 
 * @license     http://www.magepow.com/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-01-05 10:40:51
 * @@Modify Date: 2018-03-27 11:32:32
 * @@Function:
 */

namespace Magepow\MultiTranslate\Block\Adminhtml\Catalog\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $_objectFactory;
    protected $_categoryFactory;
    protected $_productFactory;
    protected $_wysiwygConfig;
    protected $_storeManager;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\DataObjectFactory $objectFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Catalog\Model\ProductFactory $productFactory, 
        \Magento\Catalog\Model\CategoryFactory $categoryFactory, 
        array $data = []
    ) {
        $this->_objectFactory = $objectFactory;
        $this->_productFactory = $productFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_storeManager  = $context->getStoreManager();
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('category');
        $products = $model->getProductCollection()->addAttributeToSelect('*');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            array(
                'data' => array(
                    'id' => 'edit_form',
                    'action' => $this->getUrl('*/*/save', ['store' => $this->getRequest()->getParam('store')]),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data',
                ),
            )
        );
        $form->setUseContainer(true);
        $form->setHtmlIdPrefix('magic_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Category Information')]);

        if ($model->getId()) {
            $fieldset->addField('entity_id', 'hidden', ['name' => 'entity_id']);
        }

        $product = $this->_productFactory->create();

        $stores = $this->_storeManager->getStores();
        $data = $model->getData();
        $data_form = $data;
        foreach ($products as $item) {
            $product_id = $item->getId();
            $_product   = $product->setStoreId(0)->load($product_id);

            $_data      = $_product->getData();
            foreach ($_data as $key => $value) {
                $key = $key . '_' . $product_id;
                $data_form[$key] = $value;
            }
            $filed = 'name_' . $product_id;
            $name = $fieldset->addField($filed, 'text',
                [
                    'label' => __("Name (Default)"),
                    'title' => __("Name (Default)"),
                    'name'  => $filed,
                    'required' => true,
                ]
            );

            
            foreach ($stores as $store) {
                $store_id   = $store->getId();
                $store_name = $store->getName();
                $group_name = $this->_storeManager->getStore($store_id)->getGroup()->getName();
                $_product   = $product->setStoreId($store_id)->load($product_id);

                $nameChecked = !$_product->getExistsStoreValueFlag('name');

                $_data      = $_product->getData();
                foreach ($_data as $key => $value) {
                    $key = $key . '_' . $product_id . '_' . $store_id;
                    $data_form[$key] = $value;
                }

                $filed = 'name_' . $product_id . '_' . $store_id;
                $name = $fieldset->addField($filed, 'text',
                    [
                        'label' => __("Name ($group_name - $store_name)"),
                        'title' => __("Name ($store_name)"),
                        'name'  => $filed,
                        'required' => true,
                    ]
                );

                $name_default = $fieldset->addField("use_default[$filed]", 'checkbox',
                    [
                        'label' => __('Default Y/N'),
                        'title' => __('Name'),
                        'name'  => "use_default[$filed]",
                        'value' => 1,
                        'checked' => $nameChecked,
                        'required' => false,
                    ]
                );

                $name_default->setAfterElementHtml(
                    '<script type="text/javascript">
                    require([
                        "jquery",
                    ],  function($){
                            $(document).ready(function($) {
                                var map     = "#'.$name_default->getHtmlId().'";
                                var depend  = "#'.$name->getHtmlId().'";                  
                                if ($(map).is(":checked")) {$(depend).prop("disabled", true); }
                                $(document).on("change", map, function() {
                                    console.log("change");
                                    if ($(this).is(":checked")){
                                        console.log("check");
                                        $(depend).prop("disabled", true);
                                    } else {
                                        console.log("ucheck");
                                        $(depend).prop("disabled", false);
                                    }
                                });
                            })
                    })
                    </script>'
                );

            }
        }

        $form->addValues($data_form);

        $this->setForm($form);

        return parent::_prepareForm();
    }

}
