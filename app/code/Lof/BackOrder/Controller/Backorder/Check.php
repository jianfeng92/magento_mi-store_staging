<?php
namespace Lof\BackOrder\Controller\Backorder;


class Check extends \Magento\Framework\App\Action\Action
{
	protected $_helper;
	
	
 
	public function __construct(
		\Magento\Framework\App\Action\Context $context,	
		\Lof\BackOrder\Helper\Data $helper
		)
	{
		
		$this->_helper = $helper;
		return parent::__construct($context);
	}
	
	
	
	public function execute()
	{
		$post = $this->getRequest()->getPost();
		$msg = $this->_helper->getPreOrderInfoBlock($post->product_id);
		
		if($post->product_id != '')	
		{
			$backorder = ['backorder'=>$this->_helper->isBackorder($post->product_id),"msg" => $msg];	
			
		}			
		else
		{
			$backorder = ['backorder'=>false, "msg" => $msg];	
		}
		
		
		
		echo json_encode($backorder);
				
		
	}
	
	
	
}