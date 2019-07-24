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
 * @package    Lof_PreOrder
 * @copyright  Copyright (c) 2018 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */


namespace Lof\BackOrder\Helper;



class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	
	

     /**
     * @var \Magento\Catalog\Model\ProductFactor
     */
    protected $_product;

    public $_resource;
	
   
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\App\ResourceConnection $resource,
        \Magento\Catalog\Model\ProductFactory $product	
    ) {
        parent::__construct($context);
        $this->_urlBuilder = $context->getUrlBuilder();
		$this->_resource = $resource;
        $this->_product = $product;
       
    }
    
	
	/**
     * Check Product is Preorder or Not.
     *
     * @param int  $productId
     * @param bool $stockStatus [optional]
     *
     * @return bool
     */
    public function isBackorder($productId, $stockStatus = '')
    {
        $isProduct = false;
        $productId = (int) $productId;
        if (!$this->isValidProductId($productId)) {
            return false;
        }
     
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $product = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);
        if($product) {
            $isProduct = true;
        }
		
        if (!$isProduct) {
            return false;
        }
        $productType = $product->getTypeId();
		
		

        $productTypeArray = ['configurable', 'bundle', 'grouped'];
		
        if (in_array($productType, $productTypeArray)) {
            return false;
        }
        
		
		 
		$stockRegistry = $objectManager->create('Magento\CatalogInventory\Api\StockRegistryInterface');
		$stockitem = $stockRegistry->getStockItem($productId,$product->getStore()->getWebsiteId());
		
		$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
		$stock_threshold_qty = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('cataloginventory/options/stock_threshold_qty',$storeScope);
		
		if($stockitem->getQty() <= 0 && ($stockitem->getBackorders() == 1 || $stockitem->getBackorders() == 2) && $stockitem->getIsInStock())
		{
			return 1; //For Back Order
		}
		else if($stockitem->getIsInStock() && $stockitem->getQty() < $stock_threshold_qty)
		{
			return 2; //For Limited Stock
		}
		else if($stockitem->getQty() == 0 || !$stockitem->getIsInStock())
		{
			return 3; //For Out Of Stock
		}
		
		
		
		
		
        return false;
    }
	
	
	   /**
     * Get Stock Status of Product.
     *
     * @param int $productId
     *
     * @return bool
     */
    public function getStockStatus($productId)
    {
        $stockDetails = $this->getStockDetails($productId);

        return $stockDetails['is_in_stock'];
    }
	
	/**
     * Get Stock Details of Product.
     *
     * @param int $productId
     *
     * @return array
     */
    public function getStockDetails($productId)
    {
		
        $connection = $this->_resource->getConnection();
        $stockDetails = ['is_in_stock' => 0, 'qty' => 0, 'backorders' => 0];
        $collection = $this->_productCollection
                            ->create()
                            ->addAttributeToSelect('name');
        $table = $connection->getTableName('cataloginventory_stock_item');
        $bind = 'product_id = entity_id';
        $cond = '{{table}}.stock_id = 1';
        $type = 'left';
        $alias = 'is_in_stock';
        $field = 'is_in_stock';
        $collection->joinField($alias, $table, $field, $bind, $cond, $type);
        $alias = 'qty';
        $field = 'qty';
        $collection->joinField($alias, $table, $field, $bind, $cond, $type);
        $collection->addFieldToFilter('entity_id', $productId);
        foreach ($collection as $value) {
            $stockDetails['qty'] = $value->getQty();
            $stockDetails['is_in_stock'] = $value->getIsInStock();
            $stockDetails['backorders'] = $value->getBackorders();
            $stockDetails['name'] = $value->getName();
        }
		
		
        return $stockDetails;
    }

    /**
     * Get current url
     */
    public function getCurrentUrls() {
        return $this->_urlBuilder->getCurrentUrl();
    }

   

    public function formatDate(
        $date = null,
        $format = \IntlDateFormatter::SHORT,
        $showTime = false,
        $timezone = null
        ) {
        $date = $date instanceof \DateTimeInterface ? $date : new \DateTime($date);
        return $this->_localeDate->formatDateTime(
            $date,
            $format,
            $showTime ? $format : \IntlDateFormatter::NONE,
            null,
            $timezone
            );
    }

    public function getFormatDate($date, $type = 'full'){
        $result = '';
        switch ($type) {
            case 'full':
            $result = $this->formatDate($date, \IntlDateFormatter::FULL);
            break;
            case 'long':
            $result = $this->formatDate($date, \IntlDateFormatter::LONG);
            break;
            case 'medium':
            $result = $this->formatDate($date, \IntlDateFormatter::MEDIUM);
            break;
            case 'short':
            $result = $this->formatDate($date, \IntlDateFormatter::SHORT);
            break;
        }
        return $result;
    }

   

    /**
     * Get Product by Id.
     *
     * @param int $productId
     *
     * @return object
     */
    public function getProduct($productId)
    {
        return $this->_product->create()->load($productId);
    }
	
	
	  /**
     * Get Prorder Complete Product Id.
     *
     * @return int
     */
    public function getPreorderCompleteProductId()
    {
        $productModel = $this->_product->create();
        $productId = (int) $productModel->getIdBySku('preorder_complete');

        return $productId;
    }

    /**
     * Check Whether Product Id is Valid or Not
     *
     * @param int $productId
     *
     * @return bool
     */
    public function isValidProductId($productId)
    {
        $preorderCompleteProductId = $this->getPreorderCompleteProductId();
        if ($productId == $preorderCompleteProductId) {
            return false;
        }
        if ($productId == '' || $productId == 0) {
            return false;
        }
        return true;
    }

    
   
    
     /**
     * Get Url To Check Configurable Product is Preorder or Not.
     *
     * @return string
     */
    public function getCheckConfigUrl()
    {
        return $this->_urlBuilder->getUrl('lofbackorder/backorder/check/');
    }
     /**
     * Check Product is Child Product or Not.
     *
     * @return bool
     */
    public function isChildProduct()
    {
        $productId = $this->_request->getParam('id');
        $productModel = $this->_product->create();
        $product = $productModel->load($productId);
        $productType = $product->getTypeID();
        $productTypeArray = ['bundle', 'grouped'];
        if (in_array($productType, $productTypeArray)) {
            return true;
        }

        return false;
    }
	
	/**
     * Get Html Block of Preorder Info Block.
     *
     * @param int $productId
     *
     * @return html
     */
    public function getPreOrderInfoBlock($productId)
    {
        $html = '';
        $flag = 0;
        $today = date('m/d/y');
        $product = $this->getProduct($productId);
        $availability = $product->getBackorderAvailability();
        if ($availability != '') {
            $date = date_create($availability);
            $dispDate = date_format($date, 'F jS, Y');
            $date = date_format($date, 'm/d/y');
            if ($date > $today) {
                $flag = 1;
            }
        }
       
        if ($flag == 1) {
            $html .= "<div class='lof-msg-box lof-info lof-availability-block'>";
            $html .= "<span class='lof-date-title'>";
            $html .= __('Available On');
            $html .= ': </span>';
            $html .= "<span class='lof-date'>".$dispDate.'</span>';
            $html .= '</div>';
        }

        return $html;
    }
    
   
    
}