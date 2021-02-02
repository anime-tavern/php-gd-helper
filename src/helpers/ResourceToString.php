<?php
	class ResourceToString{

		/**
		* Gets the image resource as a binary string
		* @param resource $imageResource
		* @param int $imageType
		* @return string
		*/
		public static function getString($imageResource, int $imageType){
			ob_start();
			switch($imageType){
				case IMAGETYPE_PNG:
					imagepng($imageResource);
					break;
				case IMAGETYPE_WEBP:
					imagewebp($imageResource);
					break;
				case IMAGETYPE_JPEG:
					imagejpeg($imageResource);
					break;
				case IMAGETYPE_GIF:
					// TODO Output animated GIF
					imagegif($imageResource);
					break;
				default:
					echo "";
					break;
			}
			$data = ob_get_contents();
			ob_end_clean();

			return $data;
		}
	}
