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

use Lof\FlatRateShipping\Api\Data\FlatRateShippingInterface;
use Magento\Framework\DataObject\IdentityInterface;
use \Magento\Framework\Model\AbstractModel;

class Shipping extends AbstractModel implements FlatRateShippingInterface, IdentityInterface
{
    /**
     * CMS page cache tag
     */
    const CACHE_TAG = 'lofflatrateshipping';

    /**
     * @var string
     */
    protected $_cacheTag = 'lofflatrateshipping';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'lofflatrateshipping';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Lof\FlatRateShipping\Model\ResourceModel\Shipping');
    }
    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getLofshippingId()];
    }
    /**
     * Get ID
     *
     * @return int|null
     */
    public function getLofshippingId()
    {
        return $this->getData(self::LOFMPSHIPPING_ID);
    }
    public function setLofshippingId($id)
    {
        return $this->setData(self::LOFMPSHIPPING_ID, $id);
    }
}
