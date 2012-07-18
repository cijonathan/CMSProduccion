<?php
class Default_Model_Imagenes
{
	var $image;
	var $image_type;
	public $x = 0;
	public $y = 0;
 
	function load($filename) {
		$image_info = getimagesize($filename);
		$this->image_type = $image_info[2];
		
		if( $this->image_type == IMAGETYPE_JPEG ){
			$this->image = imagecreatefromjpeg($filename);
		} elseif( $this->image_type == IMAGETYPE_GIF ) {
			$this->image = imagecreatefromgif($filename);
		} elseif( $this->image_type == IMAGETYPE_PNG ) {
			$this->image = imagecreatefrompng($filename);
		}
	}
	function save($filename, $image_type=IMAGETYPE_JPEG, $compression=75, $permissions=null) {
		if( $image_type == IMAGETYPE_JPEG ) {
			imagejpeg($this->image,$filename,$compression);
		} elseif( $image_type == IMAGETYPE_GIF ) {
			imagegif($this->image,$filename);
		} elseif( $image_type == IMAGETYPE_PNG ) {
			imagepng($this->image,$filename);
		}
		if( $permissions != null){
			chmod($filename,$permissions);
		}
	}
	function output($image_type=IMAGETYPE_JPEG) {
		if( $image_type == IMAGETYPE_JPEG ) {
			imagejpeg($this->image);
		} elseif( $image_type == IMAGETYPE_GIF ) {
			imagegif($this->image);
		} elseif( $image_type == IMAGETYPE_PNG ) {
			imagepng($this->image);
		}
	}
	function scale($scale) {
		$width = $this->getWidth() * $scale/100;
		$height = $this->getheight() * $scale/100;
		$this->resize($width,$height);
	}
   
	function getWidth(){ return imagesx($this->image); }
	function getHeight(){ return imagesy($this->image); }
	// function resizeToHeight($height,$x,$y){
		// $ratio = $height / $this->getHeight();
		// $width = $this->getWidth() * $ratio;
		// $this->resize($width,$height,$x,$y);
	// }
	
	function resizeToHeight($height){
		$new_image = imagecreatetruecolor($this->getWidth(), $height);
		imagecopyresampled($new_image, $this->image, 0, 0, $this->x, $this->y, $this->getWidth(), $height, $this->getWidth(), $height);
		$this->image = $new_image;
	}
 
	function resizeToWidth($width){
		$ratio = $width / $this->getWidth();
		$height = $this->getheight() * $ratio;
		
		$new_image = imagecreatetruecolor($width, $height);
		imagecopyresampled($new_image, $this->image, 0, 0, $this->x, $this->y, $width, $height, $this->getWidth(), $this->getHeight());
		$this->image = $new_image;
	}
 
   function resize($width,$height) {
      $new_image = imagecreatetruecolor($width, $height);
      imagecopyresampled($new_image, $this->image, 0, 0, $this->x, $this->y, $width, $height, $this->getWidth(), $this->getHeight());
      $this->image = $new_image;
   }      
 
}
