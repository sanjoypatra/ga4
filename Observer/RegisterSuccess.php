<?php

namespace Meetanshi\GA4\Observer;

class RegisterSuccess implements \Magento\Framework\Event\ObserverInterface
{
    protected $helper;
    protected $customerSession;

    public function __construct(
        \Meetanshi\GA4\Helper\Data $helper,
        \Magento\Customer\Model\Session $session
    ){
        $this->helper = $helper;
        $this->customerSession = $session;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            if ($this->helper->isEnable()) {
                if ($this->helper->isEnable()) {
                    $customer = $observer->getEvent()->getData('customer');
                    $this->customerSession->setSignup($customer->getId());
                }
            }
        }catch (\Exception $e){
            $this->helper->logMessage($e->getMessage());
        }
    }
}