
<?php



require_once 'googleapiphp\vendor\autoload.php';
use Google\Client;
use Google\Service\Drive;


//$folderPath='C:\laragon\www\ElectionsExporter\testfiles';
date_default_timezone_set("Europe/Athens");
$credentialsPath = 'C:\laragon\www\ExportToDrive\electionsexportCredentials.json';
checklogfile();


//"C:\laragon\www\ExportToDrive\electionsexportCredentials.json"

//$credentialsPath = 'C:\laragon\www\ElectionsExporter\credentials.json';
//$hasfiles=false;

$client=new Client();


$config = parse_ini_file('config.ini', true);
$folderPath = $config['path']['folder'];
logMessage("Έλεγχος Αρχείων στον φάκελο " . $folderPath);
//echo "O Fakelos einai " . $folderPath;





$client->setAuthConfig($credentialsPath);

$client->setScopes(array('https://www.googleapis.com/auth/drive'));
//

//$client->addScope(Google\Service\Drive::DRIVE);
//$client->addScope(Google_Service_Drive::DRIVE);
$drive=new Drive($client);

$parentFolder='1jR3fDY9ftNNg8v7YmWJFD2y1S1skLwxZ';


$files=scandir($folderPath);



logMessage("Εκκίνηση script");

$files=array_diff($files,array('.','..'));

if (count($files)  > 0 ){
    $hasfiles=true;
    //echo 'I Have Files';
    logMessage("Υπάρχουν αρχεία για ανέβασμα");
} else {
    $hasfiles=false;
    //echo ' No Files';
    logMessage("Δεν υπάρχουν αρχεία για ανέβασμα");
}


if ($hasfiles) {

    deleteDrivefiles($drive,$parentFolder);

    foreach ($files as $file) {

        //var_dump($file);
        echo $file;
        $filePath = $folderPath . '/' . $file;
        // Create a file content
        $fileMetadata = new Google_Service_Drive_DriveFile([
            'name' => "$file",
            'parents' => [$parentFolder]
        ]);


        // Upload the file
        $content = file_get_contents($filePath);
        $uploadedFile = $drive->files->create($fileMetadata, array(
            'data' => $content,
            //'mimeType' => 'application/octet-stream',
            'uploadType' => 'multipart'
        ));
        $driveFileid=$uploadedFile->getId();

        if ($driveFileid)
        {

            logMessage("Ανέβηκε το αρχείο " . $uploadedFile->name . " με id " .$driveFileid . " στο Drive ");
            logMessage("Διαγραφή του " . $uploadedFile->name . "απο το" .$filePath);
            unlink($filePath);
        }
        echo 'Uploaded file ID: ' . $uploadedFile->getId() . '<br>';
    }


}

logMessage("Τερματισμός Script");

    function deleteDrivefiles($drive, $parentfolder)
    {
        logMessage("Διαγραφή των αρχείων απο το Drive");

        $files = $drive->files->listFiles([
            'q' => "'" . $parentfolder . "' in parents and trashed=false",
        ]);

        foreach ($files->getFiles() as $file) {
            logMessage("Διαγραγή του ". $file->name . " απο το Drive");
            $drive->files->delete($file->getId());
        }

    }



function logMessage($message){



    $logPath='./log.txt';
    $timestamp=date('Y-m-d H:i:s');
    $logMessage="[$timestamp] : $message " . PHP_EOL ;
    error_log($logMessage,3,$logPath);

}


function checklogfile(){

    if (!file_exists('./log.txt')) {
        echo "Den ypaxei log";
        fopen('log.txt', 0777, true);

    }
    echo "Yparxei";

}




























?>