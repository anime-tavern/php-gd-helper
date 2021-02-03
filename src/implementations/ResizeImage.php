<?php
	require_once __DIR__ . "/../GDHelper.php";
	require_once __DIR__ . "/../helpers/ResourceToString.php";

	class ResizeImage{

		public GDHelper $gdHelper;

		public function __construct(GDHelper $gdHelper){
			$this->gdHelper = $gdHelper;
		}

		/**
		* Rotates an image in degrees
		* @param int $x
		* @param int $y
		* @return GDHelper A new instance of GDHelper with the cropped image
		*/
		public function resize(int $x, int $y){
			$newCanvas = imagecreatetruecolor($x, $y);

			// Handle transparencies
			$imageType = $this->gdHelper->imageType;
			if ($imageType === IMAGETYPE_PNG || $imageType === IMAGETYPE_GIF || $imageType === IMAGETYPE_WEBP){
				// Save the transparency of a PNG
				$background = imagecolorallocate($newCanvas, 0, 0, 0);
				imagecolortransparent($newCanvas, $background);
				imagealphablending($newCanvas, false);
				imagesavealpha($newCanvas, true);
			}

			// Crop the image
			imagecopyresampled(
				$newCanvas, $this->gdHelper->resource,
				0, 0, // Canvas rectangle start location
				0, 0, // Original image start location
				$x, $y, // Canvas width/height
				$this->gdHelper->width, $this->gdHelper->height  // Original image width/height
			);

			return new GDHelper(ResourceToString::getString($newCanvas, $imageType));
		}

	}
