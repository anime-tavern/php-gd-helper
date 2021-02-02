<?php
	require_once __DIR__ . "/../src/GDHelper.php";

	$imagePath = __DIR__ . "/example.png";
	$gdHelper = GDHelper::fromFilePath($imagePath, IMAGETYPE_PNG);
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
