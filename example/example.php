<?php
	require_once __DIR__ . "/../src/GDHelper.php";

	$pngImagePath = __DIR__ . "/example.png";
	$gifImagePath = __DIR__ . "/example.gif";
	$gdHelper = GDHelper::fromFilePath($pngImagePath);

	$gdHelper_gif = GDHelper::fromFilePath($gifImagePath);
	$rotatedGif = $gdHelper_gif->rotate(45);

	$croppedGDHelper = $gdHelper->cropFromCenter(300,300);
	$rotatedImage = $croppedGDHelper->rotate(90);
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
