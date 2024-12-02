<?php

use App\Helpers\CommonHelper;

if (!function_exists('decryptData')) {
    function decryptData(string $encrypted): ?string
    {
        $helper = new CommonHelper();
        return $helper->decryptData($encrypted);
    }
}
