<?php


namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class LocalStorage implements StorageInterface
{

    /**
     * @param File|UploadedFile $file
     * @param $filename
     * @return string
     */
    public function save(File $file, $filename)
    {
        $path = "uploads/";
        return $file->move($path, $filename);
    }
}