<?php


namespace AppBundle\Service;


use Symfony\Component\HttpFoundation\File\UploadedFile;

interface ObjectStorageHelper
{
    public function upload(UploadedFile $uploadedFile, $fileName);
}