<?php
class Vijaystore_Customericon_Helper_Data extends Mage_Core_Helper_Abstract
{
	const XML_PATH_CUS_ICON_ENABLE   = 'customericon_tab/customericon_setting/customericon_active';
	const XML_PATH_CUS_ICON_WIDTH_ENABLE   = 'customericon_tab/customericon_setting/customericon_width';
	const XML_PATH_CUS_ICON_HEIGHT_ENABLE   = 'customericon_tab/customericon_setting/customericon_height';

	public function conf($code, $store = null) {
        return Mage::getStoreConfig($code, $store);
    }
	
	public function customer_icon_enable() {
		return $this->conf(self::XML_PATH_CUS_ICON_ENABLE, $store);
	}
	
	public function customer_icon_width() {
		return $this->conf(self::XML_PATH_CUS_ICON_WIDTH_ENABLE, $store);
	}
	
	public function customer_icon_height() {
		return $this->conf(self::XML_PATH_CUS_ICON_HEIGHT_ENABLE, $store);
	}
}

?>