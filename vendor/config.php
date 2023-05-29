<?php

function saveFolderPath($folderPath) {
    $config = array(
        'selected_folder' => $folderPath
    );

    $configFile = 'config.txt';
    $configContent = serialize($config);
    file_put_contents($configFile, $configContent);
}

function getFolderPath() {
    $configFile = 'config.txt';
    if (file_exists($configFile)) {
        $configContent = file_get_contents($configFile);
        $config = unserialize($configContent);
        if (isset($config['selected_folder'])) {
            return $config['selected_folder'];
        }
    }
    return null;
}

?>