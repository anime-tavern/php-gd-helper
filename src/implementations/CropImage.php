<?php
	require_once __DIR__ . "/../GDHelper.php";
	require_once __DIR__ . "/../helpers/ResourceToString.php";

	class CropImage{

		public GDHelper $gdHelper;

		public function __construct(GDHelper $gdHelper){
			$this->gdHelper = $gdHelper;
		}

		/**
		* Crops the gdHelper image
		* @return GDHelper A new instance of GDHelper with the cropped image
		*/
		public function crop(int $topX, int $topY, int $bottomX, int $bottomY){
			$newWidth = $bottomX - $topX;
			$newHeight = $bottomY - $topY;
			$croppedCanvas = imagecreatetruecolor($newWidth, $newHeight);

			// Handle transparencies
			$imageType = $this->gdHelper->imageType;
			if ($imageType === IMAGETYPE_PNG || $imageType === IMAGETYPE_GIF || $imageType === IMAGETYPE_WEBP){
				// Save the transparency of a PNG
				$background = imagecolorallocate($croppedCanvas, 0, 0, 0);
				imagecolortransparent($croppedCanvas, $background);
				imagealphablending($croppedCanvas, false);
				imagesavealpha($croppedCanvas, true);
			}

			// Crop the image
			imagecopyresampled(
				$croppedCanvas, $this->gdHelper->resource,
				0, 0, // Canvas rectangle start location
				$topX, $topY, // Original image start location
				$newWidth, $newHeight, // Canvas width/height
				$newWidth, $newHeight  // Original image width/height
			);

			return new GDHelper(ResourceToString::getString($croppedCanvas, $imageType));
		}

		/**
		* Crops an image from the center
		* @return GDHelper new instance
		*/
		public function cropFromCenter(int $sizeX, int $sizeY){
			$centerX = round($this->gdHelper->width/2);
			$centerY = round($this->gdHelper->height/2);
			$topX = $centerX - ($sizeX/2);
			$topY = $centerY - ($sizeY/2);
			$bottomX = $centerX + ($sizeX/2);
			$bottomY = $centerY + ($sizeY/2);
			return $this->crop($topX, $topY, $bottomX, $bottomY);
		}
	}
