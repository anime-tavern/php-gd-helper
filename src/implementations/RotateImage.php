<?php
	require_once __DIR__ . "/../GDHelper.php";
	require_once __DIR__ . "/../helpers/ResourceToString.php";

	class RotateImage{

		public GDHelper $gdHelper;

		public function __construct(GDHelper $gdHelper){
			$this->gdHelper = $gdHelper;
		}

		/**
		* Rotates an image in degrees
		* @param float $angleInDegrees
		* @param int $backgroundFillColor (Optional) To fill space now unused by the image
		* @return GDHelper A new instance of GDHelper with the cropped image
		*/
		public function rotate(float $angleInDegrees, int $backgroundFillColor = 0){
			$rotatedImage = null;

			// Handle transparencies
			$imageType = $this->gdHelper->imageType;
			if ($imageType === IMAGETYPE_PNG || $imageType === IMAGETYPE_GIF || $imageType === IMAGETYPE_WEBP){
				imagealphablending($this->gdHelper->resource, false);
				imagesavealpha($this->gdHelper->resource, true);
				$rotatedImage = imagerotate($this->gdHelper->resource, $angleInDegrees, imagecolorallocatealpha($this->gdHelper->resource, 0, 0, 0, 127));
				imagealphablending($rotatedImage, false);
				imagesavealpha($rotatedImage, true);
			}else{
				$rotatedImage = imagerotate($this->gdHelper->resource, $angleInDegrees, $backgroundFillColor);
			}

			return new GDHelper(ResourceToString::getString($rotatedImage, $imageType));
		}
		
	}
