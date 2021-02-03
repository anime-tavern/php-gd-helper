<?php
	require_once __DIR__ . "/../GDHelper.php";
	require_once __DIR__ . "/../helpers/ResourceToString.php";
	require_once __DIR__ . "/../helpers/GIFHelper/FrameIterator.php";

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
			if ($imageType === IMAGETYPE_PNG || $imageType === IMAGETYPE_WEBP){
				imagepalettetotruecolor($this->gdHelper->resource);
				imagealphablending($this->gdHelper->resource, false);
				imagesavealpha($this->gdHelper->resource, true);
				$rotatedImage = imagerotate($this->gdHelper->resource, $angleInDegrees, imagecolorallocatealpha($this->gdHelper->resource, 0, 0, 0, 127));
				imagealphablending($rotatedImage, false);
				imagesavealpha($rotatedImage, true);
			}elseif ($imageType === IMAGETYPE_GIF){
				$frameIterator = new FrameIterator($this->gdHelper);
				$frameIterator->forEachFrame(function(&$gdImage) use (&$angleInDegrees){
					// @WARNING
					// The color returned here might not always be the one you expect.
					// This could be because the image's color palette is full (Solved this with imagepalettetotruecolor)
					// Additionally, it could just be off because of the interpolation method.
					// See the line below that performs setTransparentColor()
					// and how that lime green is slightly different then the one directly below.
					// No idea how to solve this issue other than statically like is done now.
					imagepalettetotruecolor($gdImage); // To allow for allocation below to work
					$color = imagecolorallocatealpha($gdImage,
						FrameIterator::TRANSPARENT_COLOR[0],
						FrameIterator::TRANSPARENT_COLOR[1],
						FrameIterator::TRANSPARENT_COLOR[2],
						127
					);
					$rotatedGDFrame = imagerotate($gdImage, $angleInDegrees, $color);
					return $rotatedGDFrame;
				});

				return $frameIterator->finishedGDHelper;
			}else{
				$rotatedImage = imagerotate($this->gdHelper->resource, $angleInDegrees, $backgroundFillColor);
			}

			return new GDHelper(ResourceToString::getString($rotatedImage, $imageType));
		}

	}
