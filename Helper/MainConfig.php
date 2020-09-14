<?php
/**
 * Copyright © WebbyTroops Technologies. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace WebbyTroops\Core\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\ScopeInterface;

class MainConfig extends \Magento\Framework\App\Helper\AbstractHelper
{
    const V_NAME = 'WebbyTroops';
    
    /**
     * valid key Path
     */
    const CONFIG_VALID_KEY_PATH = 'general/valid_license_key';

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    protected $lru;
    
    /**
     *
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $encryptor;
    
    /**
     *
     * @var \Magento\Config\Model\ResourceModel\Config
     */
    protected $resourceConfig;

    /**
     * Initialize helper
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager,
     * @param Context                             $context
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\Module\ModuleListInterface $moduleList,  
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Backend\Model\Session $backendSession,
        \Magento\Config\Model\ResourceModel\Config $resourceConfig,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor
    ) {
        $this->objectManager = $objectManager;
        $this->storeManager = $storeManager;
        $this->moduleList = $moduleList;
        $this->curl = $curl;
        $this->backendSession = $backendSession;
        $this->resourceConfig = $resourceConfig;
        $this->encryptor = $encryptor;
        $this->lru = implode(
            '', array_map(
                'ch' . 'r', [104, 116, 116, 112, 115, 58, 47, 47, 115, 116, 111, 114, 101, 46, 119, 101, 98, 98, 121, 116, 114, 111, 111, 112, 115, 46, 99, 111, 109, 47]
            )
        );
        parent::__construct($context);
    }

    public function validateKey($hint)
    {
        $localhostIPs = array(
            '127.0.0.1',
            '::1'
        );
        
        if(in_array($_SERVER['REMOTE_ADDR'], $localhostIPs)){
            $res = new \stdClass;
            $res->status = 200;
            $this->backendSession->setIsKeyValid($hint.' = 2');
            return $res;
        }
        
        $moduleHelper = $this->getModuleHelper($hint); 
        $iesg = 'get'.'S'.'ect'.'ionId';
        $cv = null;
        if (method_exists($moduleHelper, $iesg)) {
            $cv = $this->getConfig($moduleHelper->$iesg() . '/general/' . strrev('ye' . 'k_e' . 'sne' . 'cil'));
        }
        if($cv) {
            $isValid = $this->checkValidLicenseKey($cv, $hint);
            
            if($isValid){
                $res = new \stdClass;
                $res->status = 200;
                $this->backendSession->setIsKeyValid($hint.' = 1');
                return $res;
            }
            $data['validateData'] = [
                'dom'. 'ains' => rtrim($this->getBaseUrl(),"/"),
                'licen' . strrev('ye'.'k_e'.'s') => $cv,
                'module_version' => $this->getVersion($hint),
                'magento_version' => $this->getMagentoVersion(),
                'product_name' => $hint,
            ];
            
            $res = $this->checkContent($data, $hint);
            $this->backendSession->setIsKeyValid($hint.' = 0');
            if($res->status == 200){
                $this->backendSession->setIsKeyValid($hint.' = 1');
                $this->saveValidLicenseKey($cv, $hint);
            }
            $this->backendSession->setErrMsg($res->message);
            return $res;
        } else {
            $res = new \stdClass;
            $res->status = 404;
            $res->message = __('Please enter a valid license key');
            $this->backendSession->setErrMsg($res->message);
            return $res;
        }
    }
    
    /**
     * get base url
     *
     * @return array
     */
    public function getBaseUrl()
    {
        $bu  = strrev('lru_'. 'esab' . '/' . 'eruces/bew');
        return $this->getConfig($bu);
        
    }
    
    /**
     * get module version
     *
     * @return string
     */
    public function getVersion($mn)
    {
        $mv = '';
        if ($e = $this->moduleList->getOne(self::V_NAME .'_'. $mn)) {
            $mv = $e['set'.'up'.'_'.'ver'.'sion'];
        }
        return $mv;
    }
    
    /**
     * get Magento version
     *
     * @return string
     */
    public function getMagentoVersion()
    {
        $productMetadata = $this->objectManager->get('Magento\Framework\App\ProductMetadataInterface');
        return $productMetadata->getVersion();
    }
 
    /**
     * Receive helper
     *
     * @param  string $moduleName
     * @return \Magento\Framework\App\Helper\AbstractHelper
     */
    public function getModuleHelper($moduleName)
    {
        return $this->objectManager->get("WebbyTroops\\{$moduleName}\Helper\Data");
    }
    
    /**
     * get config value
     *
     * @param  string $path
     * @param string|int  $store
     * @param string|null $scope
     * @return mixed
     */
    public function getConfig($path, $store = null, $scope = null)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }
    
    /**
     * check content
     *
     * @return mixed
     */
    public function checkContent($data, $hint)
    {
        $this->curl->addHeader("Content-Type", "application/json");
        $this->curl->post($this->lru.'rest/V1/validate_key', json_encode($data));
        $status = $this->curl->getStatus();
        if($status == 200 || $status == 302){
            $result = $this->curl->getBody();
            return json_decode($result);
        } else {
            $res = new \stdClass;
            $res->status = 403;
            $res->message = __('Something went wrong.');
            $this->backendSession->setIsKeyValid($hint.' = 0');
            return $res;
        }
    }
    
    /**
     * Disable module
     *
     * @return this
     */
    public function disable($mName)
    {
        $helper = $this->getModuleHelper($mName);
        if (method_exists($helper, 'disableModule')) {
            $helper->disableModule();
        }
        return $this;
    }
    
    /**
     * Get Error Message
     *
     * @return string
     */
    public function getErrorMsg()
    {
        return $this->backendSession->getErrMsg();
    }
    
    /**
     * check if key valid from session
     *
     * @return string
     */
    public function loadModule()
    {
       return $this->backendSession->getIsKeyValid(); 
    }
    
    /**
     * save valid key
     *
     * @return void
     */
    public function saveValidLicenseKey($key, $module)
    {
        $helper = $this->getModuleHelper($module);
        if (method_exists($helper, 'getSectionId')) {
           $sectionId = $helper->getSectionId();
           $encrypted = $this->encryptor->encrypt($key);
           $this->resourceConfig->saveConfig($sectionId . '/' . self::CONFIG_VALID_KEY_PATH, $encrypted);
        }
    }
    
    /**
     * check valid license key
     *
     * @return bool
     */
    public function checkValidLicenseKey($key, $module)
    {
        $helper = $this->getModuleHelper($module);
        $isValid = false;
        if (method_exists($helper, 'getSectionId')) {
           $sectionId = $helper->getSectionId();
           $encryptedValue = $this->getConfig($sectionId . '/' . self::CONFIG_VALID_KEY_PATH);
           $decrypted = $this->encryptor->decrypt($encryptedValue);
           $isValid = $decrypted == $key;
        }
        return $isValid;
    }
    
}
