<?php
/**
 * Magepow 
 * @category    Magepow 
 * @copyright   Copyright (c) 2014 Magepow (http://www.magepow.com/) 
 * @license     http://www.magepow.com/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-01-05 10:40:51
 * @@Modify Date: 2018-03-26 21:27:50
 * @@Function:
 */

namespace Magepow\MultiTranslate\Controller\Adminhtml\Category;

class Edit extends \Magepow\MultiTranslate\Controller\Adminhtml\Category
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('category_id');
        $model = $this->_categoryFactory->create();
        if ($id) {
            $model->setStoreId(0)->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This Category no longer exists.'));
                $resultRedirect = $this->_resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $this->_coreRegistry->register('current_category', $model, 1);
        $this->_coreRegistry->register('category', $model, 1);
        $resultPage = $this->_resultPageFactory->create();

        return $resultPage;
    }
}
