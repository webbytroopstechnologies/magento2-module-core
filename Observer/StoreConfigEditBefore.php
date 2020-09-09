<?php
/**
 * Copyright © WebbyTroops Technologies. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace WebbyTroops\Core\Observer;

/**
 * StoreConfigEditBefore observer
 */
class StoreConfigEditBefore extends AbstractStoreConfig
{
    /**
     * Show messages for invalid license key
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $section = $this->getSection($observer);
        if (!$section) {
            return;
        }

        $sectionData = $this->getModuleBySection($section);
        $data = isset($sectionData['valid_data']) ? $sectionData['valid_data'] : null ;
        if(is_array($sectionData) && $data->status != 200){
            $this->mainConfig->disable($sectionData['hint']);
            $this->messageManager->addError($this->mainConfig->getErrorMsg());
        } 
    }
}
