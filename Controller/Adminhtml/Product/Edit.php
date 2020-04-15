<?php
/**
 * Magepow 
 * @category    Magepow 
 * @copyright   Copyright (c) 2014 Magepow (http://www.magepow.com/) 
 * @license     http://www.magepow.com/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-01-05 10:40:51
 * @@Modify Date: 2018-03-21 18:56:36
 * @@Function:
 */

namespace Magepow\MultiTranslate\Controller\Adminhtml\Product;

class Edit extends \Magepow\MultiTranslate\Controller\Adminhtml\Product
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('product_id');
        $model = $this->_productFactory->create();

        if ($id) {
            $model->setStoreId(0)->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This Product no longer exists.'));
                $resultRedirect = $this->_resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $this->_coreRegistry->register('current_product', $model, 1);
        $this->_coreRegistry->register('product', $model, 1);
        $resultPage = $this->_resultPageFactory->create();

        return $resultPage;
    }
}
