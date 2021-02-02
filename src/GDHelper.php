<?php
	require_once __DIR__ . "/exceptions/FileNotFound.php";

	class GDHelper{

		public int $imageType;
		public string $binary;

		/**
		* Constructs an instance with a file's binary
		* contents
		* @param string $binary
		* @return GDHelper
		*/
		public function __construct(string $binary, int $imageType){
			$this->binary = $binary;
			$this->imageType = $imageType;
		}

		/**
		* Creates an instance from a file path
		* @param string $filePath
		* @param int $imageType
		* @return GDHelper
		*/
		public static function fromFilePath(string $filePath, int $imageType){
			$filePath = realpath($filePath);
			if (!$filePath){
				throw new FileNotFound();
			}

			$binary = file_get_contents($filePath);
			return new GDHelper($binary, $imageType);
		}
	}
