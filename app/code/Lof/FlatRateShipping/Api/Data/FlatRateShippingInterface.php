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

namespace Lof\FlatRateShipping\Api\Data;

interface FlatRateShippingInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const LOFMPSHIPPING_ID = 'lofshipping_id';
    /**#@-*/

    /**
     * Get FlatRateShipping ID
     *
     * @return int|null
     */
    public function getLofshippingId();
    /**
     * Set FlatRateShipping ID
     *
     * @param int $id
     * @return \Lof\FlatRateShipping\Api\Data\FlatRateShippingInterface
     */
    public function setLofshippingId($id);
}
