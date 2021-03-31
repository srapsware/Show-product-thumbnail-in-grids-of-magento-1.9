<?php
/*
NOTICE OF LICENSE

This source file is subject to the NekloEULA that is bundled with this package in the file ICENSE.txt.

It is also available through the world-wide-web at this URL: http://store.neklo.com/LICENSE.txt

Copyright (c)  Neklo (http://store.neklo.com/)
*/


class Neklo_Core_Helper_Extension extends Mage_Core_Helper_Abstract
{
    protected $_moduleConfigList = null;
    protected $_moduleList = null;

    public function getModuleList()
    {
        if ($this->_moduleList === null) {
            $moduleList = array();
            foreach ($this->getModuleConfigList() as $moduleCode => $moduleConfig) {
                $moduleList[$moduleCode] = array(
                    'name'    => $moduleConfig->extension_name ? $moduleConfig->extension_name : $moduleCode,
                    'version' => $moduleConfig->version,
                );
            }

            $this->_moduleList = $moduleList;
        }

        return $this->_moduleList;
    }

    public function getModuleConfigList()
    {
        if ($this->_moduleConfigList === null) {
            $moduleConfigList = (array)Mage::getConfig()->getNode('modules')->children();
            ksort($moduleConfigList);
            $moduleList = array();
            foreach ($moduleConfigList as $moduleCode => $moduleConfig) {
                if (!$this->_canShowExtension($moduleCode, $moduleConfig)) {
                    continue;
                }

                $moduleList[strtolower($moduleCode)] = $moduleConfig;
            }

            $this->_moduleConfigList = $moduleList;
        }

        return $this->_moduleConfigList;
    }

    /**
     * @param string $code
     * @param Mage_Core_Model_Config_Element $config
     *
     * @return bool
     */
    protected function _canShowExtension($code, $config)
    {
        if (!$code || !$config) {
            return false;
        }

        if (!($config instanceof Mage_Core_Model_Config_Element)) {
            return false;
        }

        if (!is_object($config) || !method_exists($config, 'is') || !$config->is('active', 'true')) {
            return false;
        }

        if (!$this->_isNekloExtension($code)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $code
     *
     * @return bool
     */
    protected function _isNekloExtension($code)
    {
        return (strstr($code, 'Neklo_') !== false);
    }
}
