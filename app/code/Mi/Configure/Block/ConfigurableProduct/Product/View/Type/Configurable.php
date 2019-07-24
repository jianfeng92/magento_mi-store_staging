<?php

namespace Mi\Configure\Block\ConfigurableProduct\Product\View\Type;

use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Json\DecoderInterface;

class Configurable
{
    protected $jsonEncoder;
    protected $jsonDecoder;
    protected $_productRepository;

    public function __construct(
            \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
            EncoderInterface $jsonEncoder,
            DecoderInterface $jsonDecoder
    ) {
        $this->jsonDecoder = $jsonDecoder;
        $this->jsonEncoder = $jsonEncoder;
        $this->_productRepository = $productRepository;
    }

    public function getProductById($id)
    {
        return $this->_productRepository->getById($id);
    }

    public function aroundGetJsonConfig(
        \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject,
        \Closure $proceed
    )
    {
        $sname = [];
        $smpn = [];

        $config = $proceed();
        $config = $this->jsonDecoder->decode($config);

        foreach ($subject->getAllowProducts() as $prod) {
            $id = $prod->getId();
            $product = $this->getProductById($id);
            $sname[$id] = $product->getName();
            $attributes = $product->getAttributes();
            $smpn[$id] = $product->getResource()->getAttribute('mpn')->getFrontend()->getValue($product);
            $smpnLable[$id] = $product->getResource()->getAttribute('mpn')->getFrontend()->getLabel($product);
        }
        $config['sname'] = $sname;
        $config['smpn'] = $smpn;
        $config['smpn_label'] = $smpnLable;

        return $this->jsonEncoder->encode($config);
    }
}