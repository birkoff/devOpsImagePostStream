<?php


namespace AppBundle\Service;


use AppBundle\Exceptions\InvalidExtensionException;
use AppBundle\Exceptions\InvalidFileSizeException;
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
     * @throws InvalidExtensionException
     * @throws InvalidFileSizeException
     */
    public function handleUpload(UploadedFile $file)
    {
        if(!in_array($file->getMimeType(), ['image/jpeg', 'image/gif', 'image/png'])) {
            throw new InvalidExtensionException($file->getMimeType());
        }

        if($file->getSize() > 200000) {
            throw new InvalidFileSizeException($file->getSize());
        }

        $filename = uniqid() . "." . $file->getClientOriginalExtension();
        $path = "images/";
        $file->move($path, $filename);
        return $path . $filename;
    }
}