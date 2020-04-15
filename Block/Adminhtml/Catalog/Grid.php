<?php
/**
 * Magepow 
 * @category    Magepow 
 * @copyright   Copyright (c) 2014 Magepow (http://www.magepow.com/) 
 * @license     http://www.magepow.com/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-01-05 10:40:51
 * @@Modify Date: 2018-03-26 21:46:03
 * @@Function:
 */

namespace Magepow\MultiTranslate\Block\Adminhtml\Catalog;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
 
    /**
     * @var Category
     */
    protected $_categoryInstance;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryFactory;
    protected $_collectionFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Type
     */
    protected $_type;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $_status;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_visibility;

    /**
     * construct.
     *
     * @param \Magento\Backend\Block\Template\Context                         $context
     * @param \Magento\Backend\Helper\Data                                    $backendHelper
     * @param \Magento\Catalog\Model\CategoryFactory                           $categoryFactory
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status          $status
     * @param array                                                           $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Data\CollectionFactory $collectionFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $status,
    
        array $data = []
    ) {
        $this->_categoryFactory = $categoryFactory;
        $this->_collectionFactory = $collectionFactory;
        $this->_categoryInstance = $categoryFactory->create();
        $this->_status = $status;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('categoryGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setPagerVisibility(false);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        
        $categories = $this->_categoryInstance->getCollection()
                        ->addAttributeToSelect(array('entity_id','name'));

        $collection = $this->_collectionFactory->create();
        foreach ($categories as $item) {
            $varienObject = new \Magento\Framework\DataObject();
            $varienObject->setData($item->getData());
            $varienObject->setData('id', $item->getEntityId());
            $collection->addItem($varienObject);
        }
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'index' => 'name',
                'class' => 'xxx'
            ]
        );

        $this->addColumn(
            'edit',
            [
                'header' => __('Edit'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => ['base' => '*/*/edit'],
                        'field' => 'category_id',
                    ],
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action',
            ]
        );
        // $this->addExportType('*/*/exportCsv', __('CSV'));
        // $this->addExportType('*/*/exportXml', __('XML'));
        // $this->addExportType('*/*/exportExcel', __('Excel'));

        return parent::_prepareColumns();
    }

    /**
     * get slider vailable option
     *
     * @return array
     */

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        // $this->setMassactionIdField('entity_id');
        // $this->getMassactionBlock()->setFormFieldName('multitranslate');

        // $this->getMassactionBlock()->addItem(
        //     'delete',
        //     [
        //         'label' => __('Delete'),
        //         'url' => $this->getUrl('multitranslate/*/massDelete'),
        //         'confirm' => __('Are you sure?'),
        //     ]
        // );

        // $statuses = $this->_status->getOptionArray();

        // array_unshift($statuses, ['label' => '', 'value' => '']);
        // $this->getMassactionBlock()->addItem(
        //     'status',
        //     [
        //         'label' => __('Change status'),
        //         'url' => $this->getUrl('multitranslate/*/massStatus', ['_current' => true]),
        //         'additional' => [
        //             'visibility' => [
        //                 'name' => 'status',
        //                 'type' => 'select',
        //                 'class' => 'required-entry',
        //                 'label' => __('Status'),
        //                 'values' => $statuses,
        //             ],
        //         ],
        //     ]
        // );

        return $this;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }

    /**
     * get row url
     * @param  object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl(
            '*/*/edit',
            ['category_id' => $row->getId()]
        );
    }
}
