<?php
	require_once __DIR__ . "/../GDHelper.php";
	require_once __DIR__ . "/../helpers/ResourceToString.php";
	require_once __DIR__ . "/../helpers/GIFEnDec/IO/PhpStream.php";
	require_once __DIR__ . "/../helpers/GIFEnDec/IO/MemoryStream.php";
	require_once __DIR__ . "/../helpers/GIFEnDec/IO/FileStream.php";
	require_once __DIR__ . "/../helpers/GIFEnDec/Events/FrameDecodedEvent.php";
	require_once __DIR__ . "/../helpers/GIFEnDec/Geometry/Point.php";
	require_once __DIR__ . "/../helpers/GIFEnDec/Geometry/Rectangle.php";
	require_once __DIR__ . "/../helpers/GIFEnDec/Decoder.php";
	require_once __DIR__ . "/../helpers/GIFEnDec/Encoder.php";
	require_once __DIR__ . "/../helpers/GIFEnDec/Frame.php";
	require_once __DIR__ . "/../helpers/GIFEnDec/Color.php";

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
				imagealphablending($this->gdHelper->resource, false);
				imagesavealpha($this->gdHelper->resource, true);
				$rotatedImage = imagerotate($this->gdHelper->resource, $angleInDegrees, imagecolorallocatealpha($this->gdHelper->resource, 0, 0, 0, 127));
				imagealphablending($rotatedImage, false);
				imagesavealpha($rotatedImage, true);
			}elseif ($imageType === IMAGETYPE_GIF){
				$gifFileResource = tmpfile();
				fwrite($gifFileResource, $this->gdHelper->binary);
				$fileStream = new GIFEndec\IO\FileStream(stream_get_meta_data($gifFileResource)['uri']);
				$gifEncoder = new GIFEndec\Encoder();
				$gifDecoder = new GIFEndec\Decoder($fileStream);
				$gifDecoder->decode(function(GIFEndec\Events\FrameDecodedEvent $event) use (&$angleInDegrees, $gifEncoder){
					$frame = $event->decodedFrame;
					$gdFrame = $frame->createGDImage();

					// @WARNING
					// The color returned here might not always be the one you expect.
					// This could be because the image's color palette is full.
					// Additionally, it could just be off because of the interpolation method.
					// See the line below that performs setTransparentColor()
					// and how that lime green is slightly different then the one directly below.
					// No idea how to solve this issue other than statically like is done now.
					$color = imagecolorallocatealpha($gdFrame,50,205,50,127);
					$rotatedGDFrame = imagerotate($gdFrame, $angleInDegrees, $color);

					// Write the now rotated frame into memory
					$rotatedBinary = ResourceToString::getString($rotatedGDFrame, IMAGETYPE_GIF);
					$stream = new GIFEndec\IO\MemoryStream();
					$stream->writeString($rotatedBinary);
					$rotatedFrame = new GIFEndec\Frame();
					$rotatedFrame->setDisposalMethod(1);
					$rotatedFrame->setStream($stream);
					$rotatedFrame->setDuration($frame->getDuration());
					$rotatedFrame->setTransparentColor(new GIFEndec\Color(52,206,52));
					$rotatedFrame->setTransparent(true);//$frame->isTransparent());
					$gifEncoder->addFrame($rotatedFrame);
				});

				$gifEncoder->addFooter();
				return new GDHelper($gifEncoder->getStream()->getContents());
			}else{
				$rotatedImage = imagerotate($this->gdHelper->resource, $angleInDegrees, $backgroundFillColor);
			}

			return new GDHelper(ResourceToString::getString($rotatedImage, $imageType));
		}

	}
