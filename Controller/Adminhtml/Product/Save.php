<?php
/**
 * Magepow 
 * @category    Magepow 
 * @copyright   Copyright (c) 2014 Magepow (http://www.magepow.com/) 
 * @license     http://www.magepow.com/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-01-05 10:40:51
 * @@Modify Date: 2018-03-26 17:31:12
 * @@Function:
 */

namespace Magepow\MultiTranslate\Controller\Adminhtml\Product;

class Save extends \Magepow\MultiTranslate\Controller\Adminhtml\Product
{

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $resultRedirect = $this->_resultRedirectFactory->create();

        if ($data = $this->getRequest()->getPostValue()) {

            $model = $this->_productFactory->create();

            if ($id = $this->getRequest()->getParam('entity_id')) {
                $model->setStoreId(0)->load($id); //default
                $model->addData($data);
                try {
                        $model->save();
                    } catch (\Exception $e) {
                        $this->messageManager->addError($e->getMessage());
                        $this->messageManager->addException($e, __('Something went wrong while saving the product.'));
                    }            
                $stores = $this->_storeManager->getStores();
                foreach ($stores as $store) {
                    $_storeId = $store->getId();
                    $model->setStoreId($_storeId)->load($id);
                    $_data = array();
                    // $_data['entity_id'] = $data['entity_id'];
                    
                    /**
                     * Check "Use Default Value" checkboxes values
                     */
                    if (isset($data['use_default']["name_$_storeId"])) {
                        $model->setData('name', null);
                        // if ($model->hasData('use_config_name')) {
                            $model->setData('use_config_name', false);
                        // }
                    } else {
                        $model->setData('name', $data["name_$_storeId"]);
                    }

                    if (isset($data['use_default']["description_$_storeId"])) {
                        $model->setData('description', null);
                        // if ($model->hasData('use_config_description')) {
                            $model->setData('use_config_description', false);
                        // }
                    } else {   
                        $model->setData('description', $data["description_$_storeId"]);
                    }
                    if (isset($data['use_default']["short_description_$_storeId"])) {
                        $model->setData('short_description', null);
                        // if ($model->hasData('use_config_short_description')) {
                            $model->setData('use_config_short_description', false);
                        // }
                    } else {
                         $model->setData('short_description', $data["short_description_$_storeId"]);
                    }

                    // foreach ($useDefaults as $attributeCode => $useDefaultState) {
                    //     if ($useDefaultState) {
                    //         $product->setData($attributeCode, null);
                    //         // UI component sends value even if field is disabled, so 'Use Config Settings' must be reset to false
                    //         if ($product->hasData('use_config_' . $attributeCode)) {
                    //             $product->setData('use_config_' . $attributeCode, false);
                    //         }
                    //     }
                    // }
                    // if (isset($data['use_default']["name_$_storeId"])){
                    //     $model->setData('name', null);
                    //     $model->setData('use_config_name', false);
                    // }
 
                    $model->setStoreViewId($_storeId);
                    try {
                            $model->save();
                        } catch (\Exception $e) {
                            $this->messageManager->addError($e->getMessage());
                            $this->messageManager->addException($e, __('Something went wrong while saving the product.'));
                        }
                }
            }

            try {
                $model->save();

                $this->messageManager->addSuccess(__('The Product has been saved.'));
                $this->_getSession()->setFormData(false);
                $storeViewId = $this->_storeManager->getStore()->getId();
                if ($this->getRequest()->getParam('back') === 'edit') {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        [
                            'product_id' => $model->getId(),
                            '_current' => true,
                            'store' => $storeViewId,
                            'current_product_id' => $this->getRequest()->getParam('current_product_id'),
                            'saveandclose' => $this->getRequest()->getParam('saveandclose'),
                        ]
                    );
                } elseif ($this->getRequest()->getParam('back') === 'new') {
                    return $resultRedirect->setPath(
                        '*/*/new',
                        ['_current' => TRUE]
                    );
                }

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->messageManager->addException($e, __('Something went wrong while saving the product.'));
            }

            $this->_getSession()->setFormData($data);

            return $resultRedirect->setPath(
                '*/*/edit',
                ['product_id' => $this->getRequest()->getParam('product_id')]
            );
        }

        return $resultRedirect->setPath('*/*/');
    }
}
