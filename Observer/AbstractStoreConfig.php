<?php
/**
 * Copyright © WebbyTroops Technologies. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace WebbyTroops\Core\Observer;

/**
 * AbstractStoreConfig observer
 */
abstract class AbstractStoreConfig implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var \Magento\Framework\Message\Manager
     */
    protected $messageManager;

    /**
     * @var \Magento\Config\Model\Config\Structure
     */
    protected $configStructure;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfigInterface;
 
    /**
     * @var \WebbyTroops\Core\Helper\MainConfig 
     */
    protected $mainConfig;

    /**
     * Initialize model
     *
     * @param \Magento\Framework\Message\Manager                 $messageManager
     * @param \Magento\Config\Model\Config\Structure             $configStructure
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
     * @param \WebbyTroops\Core\Helper\MainConfig $mainConfig
     */
    public function __construct(
        \Magento\Framework\Message\Manager $messageManager,
        \Magento\Config\Model\Config\Structure $configStructure,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \WebbyTroops\Core\Helper\MainConfig $mainConfig
    ) {
        $this->messageManager       = $messageManager;
        $this->configStructure      = $configStructure;
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->mainConfig           = $mainConfig;
    }

    /**
     * Receive secttion
     *
     * @param  \Magento\Framework\Event\Observer $observer
     * @return mixed
     */
    protected function getSection($observer)
    {
        $controller = $observer->getEvent()->getControllerAction();
        $req     = $controller->getRequest();
        $current = $req->getParam('section');
        $website = $req->getParam('website');
        $store   = $req->getParam('store');
        

        if (!$current) {
            $section = $this->configStructure->getFirstSection();
        } else {
            $section = $this->configStructure->getElement($current);
        }
         

        if ($section) {
            if ($this->_hasS($section)) {
                return $section;
            }
        }

        return false;
    }

    /**
     * check if section is under WebbyTroops tab
     *
     * @param  string $section
     * @return boolean
     */
    protected function _isWTSection($section)
    {
        $data = $section->getData();
        if (isset($data['tab'])) {
            return (string) $data['tab'] == strrev('spo'.'ort'.'ybb'.'ew');
        }
        return false;
    }
    /**
     * Retrieve if section has s
     *
     * @param  mixed $section
     * @return mixed
     */
    protected function _hasS($section)
    {
        if (!$this->_isWTSection($section)) {
            return false;
        }

        $v = $this->scopeConfigInterface->getValue(
            $section->getId() . '/' . 'gen'.'eral', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            0
        );
        $kl = strrev('ye'.'k_e'.'sne'.'cil');
        
        if (is_array($v) && array_key_exists($kl, $v)) {
            return true;
        }
        $sectionData = $section->getData();
        $groupData =  $sectionData['children'];
        $generalGroupFields = isset($groupData['general']) ? $groupData['general']['children']: [];
        if (is_array($generalGroupFields) && array_key_exists($kl, $generalGroupFields)) {
            return true;
        }
        
        return false;
    }

    /**
     * Retrieve product
     *
     * @param  mixed $section
     * @return mixed
     */
    protected function getModuleBySection($section)
    {
        $w = 'licen' . strrev('ye'.'k_e'.'s'); $e = 'gen'.'eral';
        foreach ($section->getChildren() as $group) {
            if ($group->getId() == $e) {
                foreach ($group->getChildren() as $field) {
                    if ($field->getId() == $w) {
                        $iesg = strrev('ye'. 'ket'. 'adi'. 'lav');
                        return [
                            'valid_data' => $this->mainConfig->$iesg($field->getHint()),
                            'hint' => $field->getHint()
                        ];
                    }
                }
            }
        }
        return null;
    }
}
