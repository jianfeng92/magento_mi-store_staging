<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_FlatRateShipping
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\FlatRateShipping\Block\Adminhtml\Shipping\Edit\Tab;

use \Magento\Backend\Block\Widget\Tab\TabInterface;

class Main extends \Magento\Backend\Block\Widget\Form\Generic implements TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
     /**
     * @var \Lof\FlatRateShipping\Model\Config\Source\MethodTypes 
     */
    protected $methodTypes;
     /**
     * @var \Lof\FlatRateShipping\Model\Config\Source\Status 
     */
    protected $status;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Lof\FlatRateShipping\Model\Config\Source\MethodTypes $methodTypes,
        \Lof\FlatRateShipping\Model\Config\Source\Status $status,
        array $data = []
    ) {
        $this->status = $status;
        $this->methodTypes = $methodTypes; 
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('lofflatrateshipping_shipping');

        if ($this->_isAllowedAction('Lof_FlatRateShipping::shipping')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Shipping Information')]);
        
        if ($model->getId()) {
            $fieldset->addField('lofshipping_id', 'hidden', ['name' => 'lofshipping_id']);
        }
         $fieldset->addField(
            'title',
            'text', 
            [
            'name' => 'title',
            'label' => __('Title'),
            'title' => __('Title'),
            'required' => true,
            'after_element_html' => __("Enter shipping title"),
            'disabled' => $isElementDisabled
            ]
         );   
        $fieldset->addField(
            'type',
            'select', 
            [
            'name' => 'type',
            'label' => __('Type'),
            'title' => __('Type'),
            'values' => $this->methodTypes->toOptionArray(),
            'required' => true,
            'after_element_html' => __("Enter shipping type"),
            'disabled' => $isElementDisabled
            ]
         ); 
         $fieldset->addField(
            'price',
            'text', 
            [
            'name' => 'price',
            'label' => __('Price'),
            'title' => __('Price'),
            'required' => true,
            'after_element_html' => __("Enter shipping price"),
            'disabled' => $isElementDisabled
            ]
         );  
         $fieldset->addField(
            'free_shipping',
            'text', 
            [
            'name' => 'free_shipping',
            'label' => __('Free Shipping'),
            'title' => __('Free Shipping'),
            'required' => true,
            'after_element_html' => __(" Minimun order amount for free shipping"),
            'disabled' => $isElementDisabled
            ]
         ); 
          $fieldset->addField(
            'sort_order',
            'text', 
            [
            'name' => 'sort_order',
            'label' => __('Sort Order'),
            'title' => __('Sort Order'),
            'required' => true,
            'after_element_html' => __(" Enter shipping sort order"),
            'disabled' => $isElementDisabled
            ]
         ); 
           $fieldset->addField(
            'status',
            'select', 
            [
            'name' => 'status',
            'label' => __('Status'),
            'title' => __('Status'),
            'values' => $this->status->toOptionArray(),
            'required' => true,
            'after_element_html' => __(" Enter shipping status"),
            'disabled' => $isElementDisabled
            ]
         ); 
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Shipping Data');
    }
    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Shipping Data');
    }
    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }
    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
