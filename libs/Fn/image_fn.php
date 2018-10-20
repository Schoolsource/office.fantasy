<?php

class Image_Fn extends _function
{

	// Import image
	public function import($path)
	{
		switch( $this->type($path) ){
		    case 'bmp': $src = imagecreatefromwbmp($path); break;
		    case 'gif': $src = imagecreatefromgif($path); break;
		    case 'jpg': $src = imagecreatefromjpeg($path); break;
		    case 'png': $src = imagecreatefrompng($path); break;
		    default : return "Unsupported picture type!";
		}

		return $src;
	}

	public function size($path)
	{
		return getimagesize($path);
	}

	public function resize($path,$minimize=48)
	{
		
		$size = $this->size($path);

		// Calculate measurements
		if (($width = $size[0]) < ($height = $size[1])) {
				// For landscape images //y 
				$new_width = $width * ($minimize / $height);
				$new_height = $minimize;
		
				if ($new_width < $minimize) {
						
					$new_height = $new_height * ($new_height / $new_width);
					$new_width = $minimize;
				}
		}
		
		else{
		
			// For portrait and square images //x 
			$new_width = $minimize;
			$new_height = $height * ($minimize / $width);
					
					
			if ($new_height < $minimize) {
				$new_width = $new_width * ($new_width / $new_height);
				$new_height = $minimize;
			}
		}

		return array($new_width, $new_height, $width, $height);

	}

	public function minimize($path,$desired=160)
	{

		$src = $this->import($path);

		if( is_array($desired) ){

			$size = $this->resize($path, $desired[1]);
			$new_img = imagecreatetruecolor($desired[0], $desired[1]);
		}
		else{
			$size = $this->resize($path, $desired);
			$new_img = imagecreatetruecolor($size[0], $size[1]);
		}

		imagealphablending( $new_img, false );
		imagesavealpha( $new_img, true );
		imagecopyresampled($new_img, $src, 0, 0, 0, 0, $size[0], $size[1], $size[2],$size[3]);

		switch( $this->type($path) ){
			case 'bmp': imagewbmp($new_img, $path); break;
			case 'gif': imagegif($new_img, $path); break;
			case 'jpg': imagejpeg($new_img, $path); break;
			case 'png': imagepng($new_img, $path, 9); break;
		}
	}

	public function type($path)
	{
		return strtolower(substr(strrchr($path,"."),1));
	}

	public function centerarea($path){
		// $size = $this->size($path);
	}

}