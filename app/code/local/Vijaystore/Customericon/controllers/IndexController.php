<?php

class Vijaystore_Customericon_IndexController extends Mage_Core_Controller_Front_Action
{
	public function preDispatch()
	{
		parent::preDispatch();
		if (!Mage::getSingleton('customer/session')->authenticate($this)) {
			$this->setFlag('', 'no-dispatch', true);
		}
	}

    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $this->getLayout()->getBlock('head')->setTitle($this->__('Customer Custom Icon'));
        $this->renderLayout();
    }
	
	public function uploadAction()
    {
		$postData = $this->getRequest()->getParams();
		$Imgtype = explode(".", $_FILES["custom_icon"]["name"]);
		$Imagetype = $Imgtype[count($Imgtype)-1];
		$allowImage = array('jpg','jpeg','gif','png');		

		$defWidth  = Mage::helper('customericon/data')->customer_icon_width(); 
		$defHeight = Mage::helper('customericon/data')->customer_icon_height();
		
		if(isset($_FILES['custom_icon']['name']) && (file_exists($_FILES['custom_icon']['tmp_name'])) && in_array($Imagetype, $allowImage))
		{		
			$imgHeiWid = getimagesize($_FILES["custom_icon"]["tmp_name"]);
			if($imgHeiWid[0] <= $defWidth && $imgHeiWid[1] <= $defHeight )
			{
				// ** Uploading Image
				try {
					$uploader = new Varien_File_Uploader('custom_icon');
					$uploader->setAllowedExtensions($allowImage);
					$uploader->setAllowRenameFiles(false);
					$uploader->setFilesDispersion(false);
					$path = Mage::getBaseDir('media') . "/vijaystore/customericon/images/";
					$imageName = $postData['customer_id']."_custom_icon." . $Imagetype;


					$postData['image_path'] = $imageName;
					$postData['created_at'] = date('Y-m-d H:i:s');
					$postData['updated_at'] = date('Y-m-d H:i:s');
					
					$customIcon = Mage::getModel('customericon/customericon')->load($postData['customer_id'], 'customer_id');
					$customIconUpt = $customIcon;
					
					$oldImage = $customIcon->getImagePath();
					//exit;
					if($oldImage)
					{
						//** Delete old icon
						unlink($path.$oldImage);
						//unlink($path."resize/".$oldImage);
						$uploader->save($path, $imageName);
						//$this->resizeImg($imageName, $defWidth, $defHeight);					
						$updateId = $customIcon->getId();
						$data['image_path'] = $imageName;
						$data['updated_at'] = date('Y-m-d H:i:s');
						$customIconUpt = $customIconUpt->addData($data);
						$update = $customIconUpt->setId($updateId)->save();	
						
						$message = $this->__('Your Custom icon have update Successfully');
					}
					else
					{	
						$uploader->save($path, $imageName);				
						$insertId = Mage::getModel('customericon/customericon')->setData($postData)->save()->getId();
						$message = $this->__('Your Custom icon have added Successfully');
					}

					Mage::getSingleton('core/session')->addSuccess($message);
				}
				catch(Exception $e) {
					$message = $this->__($e->getMessage());
					Mage::getSingleton('core/session')->addError($message);
				}
			}
			else
			{
				$message = $this->__('This Image Size change to 50x50.');
				Mage::getSingleton('core/session')->addError($message);
			}
		}
		else{
			$message = $this->__('Disallowed file type.');
			Mage::getSingleton('core/session')->addError($message);
		}

		$this->_redirect('customericon/index/index');
    }
	
	public function resizeImg($fileName, $width, $height)
	{
		$folderURL = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."vijaystore/customericon/images/";
		$imageURL = $folderURL . $fileName;
		$basePath = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS ."vijaystore/customericon/images/". $fileName;		
		$newPath = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . "vijaystore/customericon/images/resize" . DS .$fileName;
		
		//unlink($newPath);
		
        if (file_exists($basePath) && is_file($basePath) && !file_exists($newPath)) {
            $imageObj = new Varien_Image($basePath);
            $imageObj->constrainOnly(TRUE);
            $imageObj->keepAspectRatio(FALSE);
            $imageObj->keepFrame(FALSE);
            $imageObj->resize($width, $height);
            $imageObj->save($newPath);
        }
		
		//$resizedURL = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . "vijaystore/customericon/images" . DS . $fileName;
	}
}