<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['files']) && is_array($_POST['files'])) {
        $zip = new ZipArchive();
        $zipName = 'shared_files_' . time() . '.zip';

        if ($zip->open($zipName, ZipArchive::CREATE) !== TRUE) {
            exit("Tidak bisa membuat ZIP file.");
        }

        foreach ($_POST['files'] as $file) {
            $filePath = urldecode($file);
            if (file_exists($filePath)) {
                $zip->addFile($filePath, basename($filePath));
            }
        }

        $zip->close();

        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename=' . $zipName);
        header('Content-Length: ' . filesize($zipName));
        readfile($zipName);
        unlink($zipName); // Hapus file zip setelah diunduh
        exit;
    } else {
        echo "Tidak ada file yang dipilih.";
    }
} else {
    echo "Permintaan tidak valid.";
}
?>