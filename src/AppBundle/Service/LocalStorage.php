<?php


namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class LocalStorage implements ObjectStorageHelper
{

    public function getUploadUrl()
    {
        return 'http://hector.dev:8000/api/post/create';
    }

    /**
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @return string
     */
    public function handleUpload(UploadedFile $file)
    {
        $filename = uniqid() . "." . $file->getClientOriginalExtension();
        $path = "images/";
        $file->move($path, $filename);
        return $path . $filename;
    }
}