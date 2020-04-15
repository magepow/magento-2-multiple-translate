<?php
/**
 * Magepow 
 * @category    Magepow 
 * @copyright   Copyright (c) 2014 Magepow (http://www.magepow.com/) 
 * @license     http://www.magepow.com/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-01-05 10:40:51
 * @@Modify Date: 2018-04-12 11:40:16
 * @@Function:
 */

namespace Magepow\MultiTranslate\Block\Adminhtml\Product\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $_objectFactory;
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
        array $data = []
    ) {
        $this->_objectFactory = $objectFactory;
        $this->_productFactory = $productFactory;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_storeManager  = $context->getStoreManager();
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('product');

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

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Product Information')]);

        if ($model->getId()) {
            $fieldset->addField('entity_id', 'hidden', ['name' => 'entity_id']);
        }

        $name = $fieldset->addField('name', 'text',
            [
                'label' => __('Name (Default)'),
                'title' => __('Name'),
                'name'  => 'name',
                'required' => true,
            ]
        );

        $name->setAfterElementHtml(
            '<style>
.multitranslate-product-edit [class*="field-name_"], .multitranslate-product-edit [class*="field-use_default[name_"],
.multitranslate-product-edit [class*="field-description"], .multitranslate-product-edit [class*="field-use_default[description"],
.multitranslate-product-edit [class*="field-short_description"], .multitranslate-product-edit [class*="field-use_default[short_description"]
{
  width: 50%;
  float: left;
}
.multitranslate-product-edit [class*="field-description"]{clear: both;}
            </style>'
        );


        // $fieldset->addField('use_default[name]', 'checkbox',
        //     [
        //         'label' => __('Default Y/N'),
        //         'title' => __('Name'),
        //         'name'  => 'use_default[name]',
        //         'required' => false,
        //     ]
        // );

        $fieldset->addField('description', 'editor',
            [
                'label' => __('Description (Default)'),
                'title' => __('Description'),
                'name'  => 'description',
                'config'    => $this->_wysiwygConfig->getConfig(),
                'wysiwyg'   => true,
                'required' => false,
            ]
        );

        // $fieldset->addField('use_default[description]', 'checkbox',
        //     [
        //         'label' => __('Default Y/N'),
        //         'title' => __('Name'),
        //         'name'  => 'use_default[description]',
        //         'required' => false,
        //     ]
        // );

        $fieldset->addField('short_description', 'editor',
            [
                'label' => __('Short description (Default)'),
                'title' => __('Short description'),
                'name'  => 'short_description',
                'config'    => $this->_wysiwygConfig->getConfig(),
                'wysiwyg'   => true,
                'required' => false,
            ]
        );

        // $fieldset->addField('use_default[short_description]', 'checkbox',
        //     [
        //         'label' => __('Default Y/N'),
        //         'title' => __('Name'),
        //         'name'  => 'use_default[short_description]',
        //         'required' => false,
        //     ]
        // );

        $product = $this->_productFactory->create();
        $stores = $this->_storeManager->getStores();
        $data = $model->getData();
        $data_form = $data;
        foreach ($stores as $store) {
            $store_id   = $store->getId();
            $store_name = $store->getName();
            $group_name = $this->_storeManager->getStore($store_id)->getGroup()->getName();
            $product_id = $model->getId();
            $_product   = $product->setStoreId($store_id)->load($product_id);

            $nameChecked                = !$_product->getExistsStoreValueFlag('name');
            $descriptionChecked         = !$_product->getExistsStoreValueFlag('description');
            $short_descriptionChecked   = !$_product->getExistsStoreValueFlag('short_description');

            $_data      = $_product->getData();
            foreach ($_data as $key => $value) {
                $key = $key . '_' . $store_id;
                $data_form[$key] = $value;
            }

            $name = $fieldset->addField("name_$store_id", 'text',
                [
                    'label' => __("Name ($group_name - $store_name)"),
                    'title' => __("Name ($store_name)"),
                    'name'  => "name_$store_id",
                    'required' => true,
                ]
            );

            $name_default = $fieldset->addField("use_default[name_$store_id]", 'checkbox',
                [
                    'label' => __('Default Y/N'),
                    'title' => __('Name'),
                    'name'  => "use_default[name_$store_id]",
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
                            var map_parent = $(map);
                            var depend  = "#'.$name->getHtmlId().'";                  
                            if ($(map).is(":checked")) {$(depend).prop("disabled", true); }
                            console.log($(map).is(":checked"));
                            $(document).on("change", map_parent, function() {
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

            $fieldset->addField("description_$store_id", 'editor',
                [
                    'label' => __("Description ($store_name)"),
                    'title' => __("Description ($store_name)"),
                    'name'  => "description_$store_id",
                    'config'    => $this->_wysiwygConfig->getConfig(),
                    'wysiwyg'   => true,
                    'required' => false,
                ]
            );

            $fieldset->addField("short_description_$store_id", 'editor',
                [
                    'label' => __("Short Description ($store_name)"),
                    'title' => __("Short Description ($store_name)"),
                    'name'  => "short_description_$store_id",
                    'config'    => $this->_wysiwygConfig->getConfig(),
                    'wysiwyg'   => true,
                    'required' => false,
                ]
            );

            $fieldset->addField("use_default[description_$store_id]", 'checkbox',
                [
                    'label' => __('Default Y/N'),
                    'title' => __('Description'),
                    'name'  => "use_default[description_$store_id]",
                    'value' => 1,
                    'checked' => $descriptionChecked,
                    'required' => false,
                ]
            );

            $fieldset->addField("use_default[short_description_$store_id]", 'checkbox',
                [
                    'label' => __('Default Y/N'),
                    'title' => __('Short Description'),
                    'name'  => "use_default[short_description_$store_id]",
                    'value' => 1,
                    'checked' => $short_descriptionChecked,
                    'required' => false,
                ]
            );

        }

        $form->addValues($data_form);

        $this->setForm($form);

        return parent::_prepareForm();
    }

}
