<?php
/**
 * Copyright © WebbyTroops Technologies. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace WebbyTroops\Core\Block\Adminhtml\System\Config\Form;

class License extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var \WebbyTroops\Core\Helper\MainConfig
     */
    protected $mainConfig;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \WebbyTroops\Core\Helper\MainConfig $mainConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \WebbyTroops\Core\Helper\MainConfig $mainConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->mainConfig = $mainConfig;
    }

    /**
     * Render element value
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _renderValue(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = '<td>';
        $html .= $this->_getElementHtml($element);
        $module = $element->getHint()->getText();
        $product = $this->mainConfig->loadModule();
         
        if ($product == $module.' = 1') {
            $msg = implode('', array_map('ch'.'r', explode('.', '89.111.117.114.32.108.105.99.101.110.115.101.32.107.101.121.32.105.115.32.118.97.108.105.100.46')));
            $html .= '<span><span><img title="'.$msg.'" src="'.$this->getViewFileUrl('WebbyTroops_Core::images/success_icon.gif').'" style="margin-top: 2px;float: right;" /></span></span>';
        } elseif($product == $module.' = 0') {
            $msg = implode('', array_map('ch'.'r', explode('.', '89.111.117.114.32.108.105.99.101.110.115.101.32.107.101.121.32.105.115.32.105.110.118.97.108.105.100.46')));
            $html .= '<span><span><img title="'.$msg.'" src="'.$this->getViewFileUrl('WebbyTroops_Core::images/error_icon.gif').'" style="margin-top: 2px;float: right;" /></span></span>';
            $html .= base64_decode('WW91IGNhbiBmaW5kIExpY2Vuc2UgS2V5IGluIHlvdXIgYWNjb3VudCBhdCA8YSBocmVmPSdodHRwOi8vbWFya2V0cGxhY2Uud2ViYnl0cm9vcHMub3JnL2Rvd25sb2FkYWJsZS9jdXN0b21lci9wcm9kdWN0cy8nIHRhcmdldD0nX2JsYW5rJz5tYXJrZXRwbGFjZS53ZWJieXRyb29wcy5vcmc8L2E+LiBJZiB5b3UgaGF2ZSBhbnkgcXVlc3Rpb24gcGxlYXNlIGNvbnRhY3QgYXQgPGI+c3VwcG9ydEB3ZWJieXRyb29wcy5jb208L2I+Lg==');
        }  

        $html .= '</td>';
        return $html;
    }

     
}
