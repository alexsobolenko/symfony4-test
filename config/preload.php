<?php

$preload = dirname(__DIR__) . '/var/cache/prod/App_KernelProdContainer.preload.php';
if (file_exists($preload)) {
    require $preload;
}
