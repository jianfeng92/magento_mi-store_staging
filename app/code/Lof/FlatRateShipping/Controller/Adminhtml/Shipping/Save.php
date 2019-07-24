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

namespace Lof\FlatRateShipping\Controller\Adminhtml\Shipping;

use Magento\Backend\App\Action;
use Lof\FlatRateShipping\Model\ShippingmethodFactory;
use Lof\FlatRateShipping\Model\ShippingFactory;
use Magento\MediaStorage\Model\File\UploaderFactory;

class Save extends \Magento\Backend\App\Action
{
    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;
    /**
     * @var Lof\FlatRateShipping\Model\ShippingmethodFactory
     */
    protected $_mpshippingMethod;
    /**
     * @var Lof\FlatRateShipping\Model\Shipping
     */
    protected $_mpshipping;
    /**
     * @var Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $_fileUploader;
    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $_csvReader;

    /**
     * @param Action\Context                             $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry                $registry
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        ShippingmethodFactory $shippingmethodFactory,
        ShippingFactory $mpshipping,
        UploaderFactory $fileUploader,
        \Magento\Framework\File\Csv $csvReader
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->_mpshippingMethod = $shippingmethodFactory;
        $this->_mpshipping = $mpshipping;
        $this->_fileUploader = $fileUploader;
        $this->_csvReader = $csvReader;
    }

    /**
     * Check for is allowed.
     *
     * @return bool
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
    
        if ($this->getRequest()->isPost()) {
            try {
                $shippingModel = $this->_mpshipping->create();
                if(isset($_FILES['import_file'])) {
                    
                    if (!$this->_formKeyValidator->validate($this->getRequest())) {
                        return $this->resultRedirectFactory->create()->setPath('*/*/index');
                    }
                  
                    $uploader = $this->_fileUploader->create(
                        ['fileId' => 'import_file']
                    );
                     
                    $result = $uploader->validateFile();
                   
                    $file = $result['tmp_name'];
                    $fileNameArray = explode('.', $result['name']);
                    
                    $ext = end($fileNameArray);
                    if ($file != '' && $ext == 'csv') {
                        $csvFileData = $this->_csvReader->getData($file);
                        $partnerid = 0;
                        $count = 0;
                        foreach ($csvFileData as $key => $rowData) {
                           
                            if (count($rowData) < 7  && $count == 0) {
                                $this->messageManager->addError(__('Csv file is not a valid file!'));
                                return $this->resultRedirectFactory->create()->setPath('*/*/index');
                            }
                            if ($rowData[0] == '' ||
                                $rowData[1] == '' ||
                                $rowData[2] == '' ||
                                $rowData[3] == '' ||
                                $count == 0
                            ) {
                                ++$count;
                                continue;
                            }
                            $temp = [];
                            $temp['title'] = $rowData[0];
                            $temp['type'] = $rowData[1];
                            $temp['free_shipping'] = $rowData[2];
                            $temp['sort_order'] = $rowData[3];
                            $temp['price'] = $rowData[4];
                            $temp['status'] = $rowData[5];
                            $temp['partner_id'] = $rowData[6];
                            $partnerid = $rowData[6];
                            $this->addDataToCollection($temp, $rowData, $partnerid);
                        }
                        if (($count - 1) > 1) {
                            $this->messageManager->addNotice(__('Some rows are not valid!'));
                        }
                        if (($count - 1) <= 1) {
                            $this->messageManager
                                ->addSuccess(
                                    __('Your shipping detail has been successfully Saved')
                                );
                        }
                        return $this->resultRedirectFactory->create()->setPath('*/*/index');
                    } else {
                        $this->messageManager->addError(__('Please upload Csv file'));
                    }
                } else {
                    $params = $data;
                    $partnerid = 0;
                    $id = $this->getRequest()->getParam('lofshipping_id');
                    if ($id) {
                        $shippingModel->load($id);
                        $partnerid = $shippingModel->getData('partner_id');
                        
                        if ($id != $shippingModel->getId()) {
                            throw new \Magento\Framework\Exception\LocalizedException(__('The wrong shipping is specified.'));
                        }
                    }
               
                    $shippingModel->setData($params);
                    $shippingModel->save();
                    
                    $this->messageManager->addSuccess(__('Your shipping detail has been successfully Saved'));
                    return $this->resultRedirectFactory->create()->setPath('lofflatrateshipping/shipping');
                }
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        return $this->resultRedirectFactory->create()->setPath('*/*/index');
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_FlatRateShipping::mpshipping');
    }
    public function getShippingNameById($shippingMethodName)
    {
        $entityId = 0;
        $shippingMethodModel = $this->_mpshippingMethod->create()
            ->getCollection()
            ->addFieldToFilter('method_name', $shippingMethodName);
        foreach ($shippingMethodModel as $shippingMethod) {
            $entityId = $shippingMethod->getEntityId();
        }
        return $entityId;
    }

    public function addDataToCollection($temp, $rowData, $partnerid)
    {
        $collection = $this->_mpshipping->create()
            ->getCollection()
            ->addFieldToFilter('price', $rowData[4])
            ->addFieldToFilter('sort_order', $rowData[3])
            ->addFieldToFilter('title', $rowData[0])
            ->addFieldToFilter('partner_id', $partnerid)
            ->addFieldToFilter('type', $rowData[1])
            ->addFieldToFilter('free_shipping', $rowData[2])
            ->addFieldToFilter('status', $rowData[5]);

        if ($collection->getSize() > 0) {
            foreach ($collection as $data) {
                $rowId = $data->getLofshippingId();
                $dataArray = ['price' => $rowData[4]];
                $model = $this->_mpshipping->create();
                $shippingModel = $model->load($rowId)->addData($dataArray);
                $shippingModel->setLofshippingId($rowId)->save();
            }
        } else {
            $shippingModel = $this->_mpshipping->create();
            $shippingModel->setData($temp)->save();
        }
    }
}
