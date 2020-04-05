<?php 
namespace Themevast\Newsletterpopup\Block;
class Newsletterpopup extends  \Magento\Newsletter\Block\Subscribe
{
    public function getFormActionUrl()
    {
        return $this->getUrl('newsletter/subscriber/new', ['_secure' => true]);
    }

}