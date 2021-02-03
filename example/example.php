<?php
	require_once __DIR__ . "/../src/GDHelper.php";

	$pngImagePath = __DIR__ . "/example.png";
	$gifImagePath = __DIR__ . "/example.gif";
	$gif2ImagePath = __DIR__ . "/example-2.gif";
	$webpImagePath = __DIR__ . "/example.webp";
	// $gdHelper = GDHelper::fromFilePath($pngImagePath);
	//
	// $gdHelper_gif = GDHelper::fromFilePath($gifImagePath);
	// $rotatedGif = $gdHelper_gif->rotate(45);
	// $rotatedGifBase64 = $rotatedGif->toBase64DataString();
	// $gdHelper_gif->clearResource();
	// $rotatedGif->clearResource();

	$gdHelper_webp = GDHelper::fromFilePath($webpImagePath);
	//$roatedWebP = $gdHelper_webp->rotate(90);

	// $croppedGDHelper = $gdHelper->cropFromCenter(300,300);
	// $rotatedImage = $croppedGDHelper->rotate(90);
?>
<strong>Original image</strong>
<br>
<img src="<?= $gdHelper->toBase64DataString() ?>">
<br>
<strong>Cropped image</strong>
<br>
<img src="<?= $croppedGDHelper->toBase64DataString() ?>">
<br>
<strong>Rotated image</strong>
<br>
<img src="<?= $rotatedImage->toBase64DataString() ?>">
<br>
<img src="<?= $rotatedGif->toBase64DataString() ?>">
<br>
<img src="<?= $rotatedGif2->toBase64DataString() ?>">
<br>
<img src="<?= $roatedWebP->toBase64DataString() ?>">
