<?php
/**
 * Magepow 
 * @category    Magepow 
 * @copyright   Copyright (c) 2014 Magepow (http://www.magepow.com/) 
 * @license     http://www.magepow.com/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-01-05 10:40:51
 * @@Modify Date: 2018-03-27 11:40:46
 * @@Function:
 */

namespace Magepow\MultiTranslate\Controller\Adminhtml\Catalog;

use Magento\Framework\Controller\ResultFactory;

class Save extends \Magepow\MultiTranslate\Controller\Adminhtml\Catalog
{

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $resultRedirect = $this->_resultRedirectFactory->create();

        if ($data = $this->getRequest()->getPostValue()) {
            $use_default = isset($data['use_default']) ? $data['use_default'] : array();

            if ($id = $this->getRequest()->getParam('entity_id')) {
                $_data = $data;
                if(isset($_data['use_default']))    unset($_data['use_default']);
                if(isset($_data['form_key']))       unset($_data['form_key']);
                if(isset($_data['entity_id']))      unset($_data['entity_id']);
                foreach ( $_data as $key => $value) {
                    $storeId = 0;

                    /* 2 underline fix erro underline attribute short_description */
                    $tmp = explode('__', $key);
                    if(!isset($tmp[1]) || !$tmp[1]) continue;
                    $productId =  (int) $tmp[1];
                    if(isset($tmp[2]) && $tmp[2]){
                        $storeId =  (int) $tmp[2];
                    }
                    $attributeCode = $tmp[0];
                    try {
                        $productFactory = $this->_productFactory->create();
                        $product = $productFactory->setStoreId($storeId)->load($productId);
                        $product->setStoreViewId($storeId);
                        /**
                         * Check "Use Default Value" checkboxes values
                         */
                        if (isset($data['use_default'][$key])) {
                           
                            $product->setData($attributeCode, null);
                            // if ($product->hasData("use_config_$attributeCode")) {
                                $product->setData("use_config_$attributeCode", false);
                            // }
                            $product->save();
                            $this->messageManager->addSuccess(__("The %1 Product Id %2 has changed to Use Default Value.", $attributeCode, $productId));
                        } else {
                            $product->setData($attributeCode, $value);
                            $product->save();
                            $this->messageManager->addSuccess(__("The %1 Product Id %2 has been saved.", $attributeCode, $productId));
                        }


                    }  catch (\Exception $e) {
                        $this->messageManager->addError($e->getMessage());
                        $this->messageManager->addException($e, __("Something went wrong while saving %1 the product Id %2", $attributeCode, $productId));
                    }
                }
            }
            // die('stop');
            $this->_getSession()->setFormData($data);
            if ($this->getRequest()->getParam('back') === 'edit') {
                // return $resultRedirect->setPath(
                //     '*/*/edit',
                //     [
                //         'category_id' => $this->getRequest()->getParam('entity_id'), 
                //         'p' => $this->getRequest()->getParam('p')
                //     ]
                // );
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

                $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                return $resultRedirect;
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}
