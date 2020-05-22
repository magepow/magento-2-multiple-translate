<?php
/**
 * Magepow 
 * @category    Magepow 
 * @copyright   Copyright (c) 2014 Magepow (http://www.magepow.com/) 
 * @license     http://www.magepow.com/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-01-05 10:40:51
 * @@Modify Date: 2020-05-22 11:32:32
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
        $productCollection = $model->getProductCollection()->addAttributeToSelect('*');

        $pager = $this->getLayout()->createBlock('Magento\Theme\Block\Html\Pager', 'form.pager')
                                ->setAvailableLimit(array(5=>5, 10=>10, 15=>15, 20=>20))
                                ->setShowPerPage(true)
                                ->setCollection($productCollection)
                                ->setTemplate('Magepow_MultiTranslate::html/pager.phtml');
        $this->setChild('pager', $pager);

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

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Catalog Information')]);

        if ($model->getId()) {
            $fieldset->addField('entity_id', 'hidden', ['name' => 'entity_id']);
        }

       $note = $fieldset->addField('pager', 'note', array(
            'text'     => $this->getPagerHtml()
        ));

        $note->setAfterElementHtml(
            '<style>
                .multitranslate-catalog-edit [class*="field-name_"], .multitranslate-catalog-edit [class*="field-use_default[name_"],
                .multitranslate-catalog-edit [class*="field-description"], .multitranslate-catalog-edit [class*="field-use_default[description"],
                .multitranslate-catalog-edit [class*="field-short_description"], .multitranslate-catalog-edit [class*="field-use_default[short_description"]
                {
                  width: 50%;
                  float: left;
                }
                .multitranslate-catalog-edit [class*="field-description"]{clear: both;}
            </style>'
        );

        $stores = $this->_storeManager->getStores();
        $data = $model->getData();
        $data_form = $data;
        foreach ($productCollection as $item) {
            $productId = $item->getId();
            $product = $this->_productFactory->create();
            $_product   = $product->setStoreId(0)->load($productId);

            $_data      = $_product->getData();
            foreach ($_data as $key => $value) {
                 /* 2 underline fix erro underline attribute short_description */
                $key = $key . '__' . $productId;
                $data_form[$key] = $value;
            }
            $suffix = $productId;
            $name = $fieldset->addField("name__$suffix", 'text',
                [
                    'label'     => __("Name (Default)"),
                    'title'     => __("Name (Default)"),
                    'name'      => "name__$suffix",
                    'required'  => true,
                ]
            );

            $fieldset->addField("description__$suffix", 'editor',
                [
                    'label'     => __('Description (Default)'),
                    'title'     => __('Description'),
                    'name'      => "description__$suffix",
                    'config'    => $this->_wysiwygConfig->getConfig(),
                    'wysiwyg'   => true,
                    'required'  => false,
                ]
            );

            $fieldset->addField("short_description__$suffix", 'editor',
                [
                    'label'     => __('Short description (Default)'),
                    'title'     => __('Short description'),
                    'name'      => "short_description__$suffix",
                    'config'    => $this->_wysiwygConfig->getConfig(),
                    'wysiwyg'   => true,
                    'required'  => false,
                ]
            );
            
            foreach ($stores as $store) {
                $storeId    = $store->getId();
                $storeName  = $store->getName();
                $groupName  = $this->_storeManager->getStore($storeId)->getGroup()->getName();
                $product    = $this->_productFactory->create();
                $_product   = $product->setStoreId($storeId)->load($productId);

                $nameChecked                = !$_product->getExistsStoreValueFlag('name');
                $descriptionChecked         = !$_product->getExistsStoreValueFlag('description');
                $short_descriptionChecked   = !$_product->getExistsStoreValueFlag('short_description');

                $_data   = $_product->getData();
                foreach ($_data as $key => $value) {
                     /* 2 underline fix erro underline attribute short_description */
                    $key = $key . '__' . $productId . '__' . $storeId;
                    $data_form[$key] = $value;
                }

                /* 2 underline fix erro underline attribute short_description */
                $suffix = $productId . '__' . $storeId;

                $name = $fieldset->addField("name__$suffix", 'text',
                    [
                        'label'     => __("Name ($groupName - $storeName)"),
                        'title'     => __("Name ($storeName)"),
                        'name'      => "name__$suffix",
                        'required'  => true,
                    ]
                );

                $name_default = $fieldset->addField("use_default[name__$suffix]", 'checkbox',
                    [
                        'label'     => __('Use Default Value'),
                        'title'     => __('Name'),
                        'name'      => "use_default[name__$suffix]",
                        'value'     => 1,
                        'checked'   => $nameChecked,
                        'required'  => false,
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

                $fieldset->addField("description__$suffix", 'editor',
                    [
                        'label'     => __("Description ($storeName)"),
                        'title'     => __("Description ($storeName)"),
                        'name'      => "description__$suffix",
                        'config'    => $this->_wysiwygConfig->getConfig(),
                        'wysiwyg'   => true,
                        'required'  => false,
                    ]
                );

                $fieldset->addField("short_description__$suffix", 'editor',
                    [
                        'label'     => __("Short Description ($storeName)"),
                        'title'     => __("Short Description ($storeName)"),
                        'name'      => "short_description__$suffix",
                        'config'    => $this->_wysiwygConfig->getConfig(),
                        'wysiwyg'   => true,
                        'required'  => false,
                    ]
                );

                $fieldset->addField("use_default[description__$suffix]", 'checkbox',
                    [
                        'label'     => __('Use Default Value'),
                        'title'     => __('Description'),
                        'name'      => "use_default[description__$suffix]",
                        'value'     => 1,
                        'checked'   => $descriptionChecked,
                        'required'  => false,
                    ]
                );

                $fieldset->addField("use_default[short_description__$suffix]", 'checkbox',
                    [
                        'label'     => __('Use Default Value'),
                        'title'     => __('Short Description'),
                        'name'      => "use_default[short_description__$suffix]",
                        'value'     => 1,
                        'checked'   => $short_descriptionChecked,
                        'required'  => false,
                    ]
                );
            }
        }

        $form->addValues($data_form);

        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

}
