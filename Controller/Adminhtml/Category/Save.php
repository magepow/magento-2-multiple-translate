<?php
/**
 * Magepow 
 * @category    Magepow 
 * @copyright   Copyright (c) 2014 Magepow (http://www.magepow.com/) 
 * @license     http://www.magepow.com/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-01-05 10:40:51
 * @@Modify Date: 2018-03-26 10:15:48
 * @@Function:
 */

namespace Magepow\MultiTranslate\Controller\Adminhtml\Category;

class Save extends \Magepow\MultiTranslate\Controller\Adminhtml\Category
{

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $resultRedirect = $this->_resultRedirectFactory->create();

        if ($data = $this->getRequest()->getPostValue()) {

            $model = $this->_categoryFactory->create();

            if ($id = $this->getRequest()->getParam('entity_id')) {
                $model->setStoreId(0)->load($id); //default
                $model->addData($data);
                try {
                        $model->save();
                    } catch (\Exception $e) {
                        $this->messageManager->addError($e->getMessage());
                        $this->messageManager->addException($e, __('Something went wrong while saving the category.'));
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
                        $_data['name'] = $data["name_$_storeId"];
                    }

                    if (isset($data['use_default']["description_$_storeId"])) {
                        $model->setData('description', null);
                        // if ($model->hasData('use_config_description')) {
                            $model->setData('use_config_description', false);
                        // }
                    } else {
                        $_data['description'] = $data["description_$_storeId"];
                    }

                    // foreach ($useDefaults as $attributeCode => $useDefaultState) {
                    //     if ($useDefaultState) {
                    //         $category->setData($attributeCode, null);
                    //         // UI component sends value even if field is disabled, so 'Use Config Settings' must be reset to false
                    //         if ($category->hasData('use_config_' . $attributeCode)) {
                    //             $category->setData('use_config_' . $attributeCode, false);
                    //         }
                    //     }
                    // }

                    $model->addData($_data)->setStoreViewId($_storeId);
                    try {
                            $model->save();
                        } catch (\Exception $e) {
                            $this->messageManager->addError($e->getMessage());
                            $this->messageManager->addException($e, __('Something went wrong while saving the category.'));
                        }
                }
            }

            try {
                $model->save();

                $this->messageManager->addSuccess(__('The Category has been saved.'));
                $this->_getSession()->setFormData(false);

                if ($this->getRequest()->getParam('back') === 'edit') {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        [
                            'category_id' => $model->getId(),
                            '_current' => true,
                            'store' => $storeViewId,
                            'current_category_id' => $this->getRequest()->getParam('current_category_id'),
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
                $this->messageManager->addException($e, __('Something went wrong while saving the category.'));
            }

            $this->_getSession()->setFormData($data);

            return $resultRedirect->setPath(
                '*/*/edit',
                ['category_id' => $this->getRequest()->getParam('category_id')]
            );
        }

        return $resultRedirect->setPath('*/*/');
    }
}
