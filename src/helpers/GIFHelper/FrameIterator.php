<?php
	require_once __DIR__ . "/../../GDHelper.php";
	require_once __DIR__ . "/../ResourceToString.php";
	require_once __DIR__ . "/../GIFEnDec/IO/PhpStream.php";
	require_once __DIR__ . "/../GIFEnDec/IO/MemoryStream.php";
	require_once __DIR__ . "/../GIFEnDec/IO/FileStream.php";
	require_once __DIR__ . "/../GIFEnDec/Events/FrameDecodedEvent.php";
	require_once __DIR__ . "/../GIFEnDec/Geometry/Point.php";
	require_once __DIR__ . "/../GIFEnDec/Geometry/Rectangle.php";
	require_once __DIR__ . "/../GIFEnDec/Decoder.php";
	require_once __DIR__ . "/../GIFEnDec/Encoder.php";
	require_once __DIR__ . "/../GIFEnDec/Frame.php";
	require_once __DIR__ . "/../GIFEnDec/Color.php";

	/**
	* Iterates over each animated GIF frame as a GD image
	* @author Garet C. Green
	*/
	class FrameIterator{

		const TRANSPARENT_COLOR = [50,205,50];
		const TRANSPARENT_COLOR_AFTER_INTERPOLATION = [52,206,52];

		private GDHelper $gdHelper;
		public ?GDHelper $finishedGDHelper;

		/**
		* @param GDHelper $gdHelper That contains the GIF
		*/
		public function __construct($gdHelper){
			$this->gdHelper = $gdHelper;
		}

		public function forEachFrame(callable $func){
			$gifFileResource = tmpfile();
			fwrite($gifFileResource, $this->gdHelper->binary);
			$fileStream = new GIFEndec\IO\FileStream(stream_get_meta_data($gifFileResource)['uri']);
			$gifEncoder = new GIFEndec\Encoder();
			$gifDecoder = new GIFEndec\Decoder($fileStream);
			$gifDecoder->decode(function(GIFEndec\Events\FrameDecodedEvent $event) use ($func, $gifEncoder){
				$frame = $event->decodedFrame;
				$gdFrame = $frame->createGDImage();
				$manipulatedGDImage = $func($gdFrame);

				// Write the now rotated frame into memory
				$manipulatedBinary = ResourceToString::getString($manipulatedGDImage, IMAGETYPE_GIF);
				$stream = new GIFEndec\IO\MemoryStream();
				$stream->writeString($manipulatedBinary);
				$manipulatedFrame = new GIFEndec\Frame();
				$manipulatedFrame->setDisposalMethod(1);
				$manipulatedFrame->setStream($stream);
				$manipulatedFrame->setDuration($frame->getDuration());
				$manipulatedFrame->setTransparentColor(new GIFEndec\Color(
					FrameIterator::TRANSPARENT_COLOR_AFTER_INTERPOLATION[0],
					FrameIterator::TRANSPARENT_COLOR_AFTER_INTERPOLATION[1],
					FrameIterator::TRANSPARENT_COLOR_AFTER_INTERPOLATION[2],
				));
				$manipulatedFrame->setTransparent(true);//$frame->isTransparent());
				$gifEncoder->addFrame($manipulatedFrame);
			});

			$gifEncoder->addFooter();
			$this->finishedGDHelper = new GDHelper($gifEncoder->getStream()->getContents());
		}
	}
