<?php
	require_once __DIR__ . "/../src/GDHelper.php";

	$pngImagePath = __DIR__ . "/example.png";
	$gifImagePath = __DIR__ . "/example.gif";
	$gif2ImagePath = __DIR__ . "/example-2.gif";
	$webpImagePath = __DIR__ . "/example.webp";
	$gdHelper_png = GDHelper::fromFilePath($pngImagePath);
	$croppedGDHelper = $gdHelper_png->cropFromCenter(300,300);
	$gdHelper_png->clearResource();
	$croppedGDHelper->clearResource();

	$gdHelper_gif = GDHelper::fromFilePath($gifImagePath);
	$croppedGif = $gdHelper_gif->cropFromCenter(100, 100);
	$rotatedGif = $gdHelper_gif->rotate(45);
	$gdHelper_gif->clearResource();
	$rotatedGif->clearResource();
	$croppedGif->clearResource();

	$rotatedImage = $croppedGDHelper->rotate(90);
	$rotatedImage->clearResource();

	$resizedPNG = $gdHelper_png->resize(100,100);
	$resizedPNG->clearResource();
?>
<strong>Original image</strong>
<br>
<img src="<?= $gdHelper_png->toBase64DataString() ?>">
<br>
<strong>Cropped image</strong>
<br>
<img src="<?= $croppedGDHelper->toBase64DataString() ?>">
<br>
<img src="<?= $croppedGif->toBase64DataString() ?>">
<br>
<strong>Rotated image</strong>
<br>
<img src="<?= $rotatedImage->toBase64DataString() ?>">
<br>
<img src="<?= $rotatedGif->toBase64DataString() ?>">
<strong>Resized image</strong>
<br>
<img src="<?= $resizedPNG->toBase64DataString() ?>">
