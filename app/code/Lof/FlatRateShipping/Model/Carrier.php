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

namespace Lof\FlatRateShipping\Model;

use Magento\Quote\Model\Quote\Address\RateResult\Error;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Lof\FlatRateShipping\Model\ShippingmethodFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Session\SessionManager;
use Magento\Quote\Model\Quote\Item\OptionFactory;
use Lof\FlatRateShipping\Model\FlatRateShippingFactory;
use \Magento\Framework\Unserialize\Unserialize;

class Carrier extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{
    /**
     * Code of the carrier.
     *
     * @var string
     */
    const CODE = 'lofflatrateshipping';
    /**
     * Code of the carrier.
     *
     * @var string
     */
    protected $_code = self::CODE;
     /**
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $_rateMethodFactory;

    /**
     * @var ItemPriceCalculator
     */
    private $itemPriceCalculator;
    /**
     * @var \Lof\FlatRateShipping\Model\Shipping 
     */
    protected $shipping;
    
    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param ItemPriceCalculator $itemPriceCalculator
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Lof\FlatRateShipping\Model\Carrier\Flatrate\ItemPriceCalculator $itemPriceCalculator,
        \Lof\FlatRateShipping\Model\Shipping $shipping,
        array $data = []
    ) {
        $this->shipping = $shipping;
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->itemPriceCalculator = $itemPriceCalculator;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    
    
    /**
     * @param RateRequest $request
     * @return Result|bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function collectRates(RateRequest $request)
    {
        $om  = \Magento\Framework\App\ObjectManager::getInstance();
        $quotes = [];
        $sellerRates = [];
        /** @var Result $result */
        $result = $this->_rateResultFactory->create();
        
        /*loop through all items to get seller id of each item*/
        
        /*
         * seperate each seller item to dependence array in quotes array.
        */

        if($request->getAllItems()) {
            foreach($request->getAllItems() as $item) {
                $product    = $item->getProduct()->load($item->getProductId());
                if($item->getParentItem() && $product->isVirtual()) continue;
                
                $sellerId = 'admin';

                /*Get all flatrate shipping info*/
                if(!isset($sellerRates[$sellerId])){
                    $sellerRates[$sellerId] = array();
                    $rates = $this->shipping->getCollection()->addFieldToFilter('status',1)->setOrder('sort_order','ASC');
                    if($rates){
                        foreach($rates->getData() as $rate){
                            $identifier = $rate['lofshipping_id'];
                            $sellerRates[$sellerId][$identifier] = array(
                                'title' => $rate['title'],
                                'price' => $rate['price'],
                                'type'  => $rate['type'],
                                'free_shipping' => $rate['free_shipping'],
                                'sort_order'    => $rate['sort_order'],
                            );
                        }
                    }
                }
                
                /*Get item by seller id*/
                if(!isset($quotes[$sellerId])) $quotes[$sellerId] = array();
                $quotes[$sellerId][] = $item;
                
                  
                
            }
            
          
            /*Add shipping method for each seller flatrate*/
            foreach($sellerRates as $sellerId => $rates){
                $total  = 0;
                foreach($quotes[$sellerId] as $item){
                    $product    = $item->getProduct()->load($item->getProductId());
                    if($item->getParentItem() || $product->isVirtual()) continue;
                    $total += $item->getRowTotal();
                }
            
                foreach($rates as $code => $rate){                     
                    /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
                    $method = $this->_rateMethodFactory->create();
        
                  
                    $method->setCarrier($this->_code);
                    //$method->setSellerId($sellerId);
                    $method->setCarrierTitle($this->getConfigData('title'));
                    
                    $method->setMethod($code.' '.$sellerId);
                    $method->setMethodTitle($rate['title']);
                    
                    if($rate['type'] == 'O')    {   //per order
                        $shippingPrice = $rate['price'];
                    }else{
                        $shippingPrice  = 0;
                        $qty            = 0;
                        foreach($quotes[$sellerId] as $item){
                            $product    = $item->getProduct();
                            if($product->isVirtual() || $item->getParentItem()) {
                                continue;
                            }
                            if($item->getFreeShipping()) continue;
                            $qty += $item->getQty();
                        }
                        $shippingPrice = $qty * $rate['price'];
                    }
                     
                    if($rate['free_shipping'] && $total >= $rate['free_shipping']){
                        $shippingPrice = 0;
                    }
                    
                    $sellerRates[$sellerId][$code]['shipping_price'] = $shippingPrice;
                    $method->setPrice($shippingPrice);
                    $method->setCost($shippingPrice);
                    $result->append($method);
                }
            }
        }
       
        return $result;
    }
    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        return ['lofflatrateshipping' => $this->getConfigData('name')];
    }
}
