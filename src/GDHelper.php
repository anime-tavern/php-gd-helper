<?php
	/**
	* @author Garet C. Green
	*/

	require_once __DIR__ . "/exceptions/AnimatedWebPNotSupported.php";
	require_once __DIR__ . "/exceptions/FileNotFound.php";
	require_once __DIR__ . "/exceptions/InvalidImage.php";
	require_once __DIR__ . "/implementations/CropImage.php";
	require_once __DIR__ . "/implementations/RotateImage.php";

	class GDHelper{

		public string $binary;
		public int $width;
		public int $height;
		public int $imageType;

		/** @var resource $resource Cannot type-hint Resources as of PHP 8. Is the image resource */
		public $resource;

		/**
		* Constructs an instance with a file's binary
		* contents
		* @param string $binary
		* @return GDHelper
		*/
		public function __construct(string $binary){
			$this->binary = $binary;

			// Read the first 16 bytes to handle VP8X fatal error catching
			// Animated WebP with VP8X will throw an uncatchable error.
			// So we must try to identify it first
			$firstBytes = substr($binary,0,16);
			if (str_contains($firstBytes, "VP8X")){
				throw new AnimatedWebPNotSupported("Animated WebP currently not supported by the PHP GD library.");
			}

			$this->resource = imagecreatefromstring($binary);

			// Was the image parsable?
			if ($this->resource === false){
				throw new InvalidImage("The binary passed is not a valid image type.");
			}

			// Get the image's info
			$imageInfo = getimagesizefromstring($this->binary);
			$this->width = $imageInfo[0];
			$this->height = $imageInfo[1];
			$this->imageType = $imageInfo[2];
		}

		/**
		* Rotates an image in degrees
		* @param float $angleInDegrees
		* @param int $backgroundFillColor (Optional) To fill space now unused by the image
		* @return GDHelper A new instance of GDHelper with the cropped image
		*/
		public function rotate(float $angleInDegrees, int $backgroundFillColor = 0){
			$rotateImage = new RotateImage($this);
			return $rotateImage->rotate($angleInDegrees, $backgroundFillColor);
		}

		/**
		* Clears the stored GD resource
		*/
		public function clearResource(){
			if ($this->resource !== null){
				imagedestroy($this->resource);
			}
		}

		/**
		* Crops an image
		* @return GDHelper new instance
		*/
		public function crop(int $topX, int $topY, int $bottomX, int $bottomY){
			$cropImage = new CropImage($this);
			return $cropImage->crop($topX, $topY, $bottomX, $bottomY);
		}

		/**
		* Crops an image from the center
		* @return GDHelper new instance
		*/
		public function cropFromCenter(int $sizeX, int $sizeY){
			$cropImage = new CropImage($this);
			return $cropImage->cropFromCenter($sizeX, $sizeY);
		}

		/**
		* Gets a base64 data string ready to be served
		* over an HTTP stream. As a URL, image source, etc
		* @return string
		*/
		public function toBase64DataString(){
			$base64Image = base64_encode($this->binary);

			switch ($this->imageType){
				case IMAGETYPE_JPEG:
					return "data:image/jpeg;base64,$base64Image";
					break;
				case IMAGETYPE_PNG:
					return "data:image/png;base64,$base64Image";
					break;
				case IMAGETYPE_GIF:
					return "data:image/gif;base64,$base64Image";
					break;
				case IMAGETYPE_GIF:
					return "data:image/webp;base64,$base64Image";
					break;
				default:
					break;
			}

			return "";
		}

		/**
		* Creates an instance from a file path
		* @param string $filePath
		* @param int $imageType
		* @return GDHelper
		*/
		public static function fromFilePath(string $filePath){
			$filePath = realpath($filePath);
			if (!$filePath){
				throw new FileNotFound();
			}

			$binary = file_get_contents($filePath);
			return new GDHelper($binary);
		}
	}
