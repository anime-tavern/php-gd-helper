<?php
	require_once __DIR__ . "/../src/GDHelper.php";

	$imagePath = __DIR__ . "/example.png";
	$gdHelper = GDHelper::fromFilePath($imagePath, IMAGETYPE_PNG);
	$croppedGDHelper = $gdHelper->cropFromCenter(300,300);
?>
<img src="<?= $croppedGDHelper->toBase64DataString() ?>">
