<?php

namespace AppBundle\Service;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Aws\Sdk;

date_default_timezone_set('UTC');

class AwsS3Service implements ObjectStorageHelper
{
    private $s3;

    public function  __construct()
    {
        $sdk = new Sdk([
            'profile'  => 'birkoff',
            'region'   => 'eu-west-1',
            'version'  => 'latest'
        ]);

        $this->s3 = $sdk->createS3();
    }

    public function getUploadUrl()
    {
        $bucket = 'hectors-lambda-test-ppictures';
        $dataKey = 'test_data.txt';

        $cmd = $this->s3->getCommand('GetObject', [
            'Bucket' => $bucket,
            'Key'    => $dataKey
        ]);

        $request = $this->s3->createPresignedRequest($cmd, '+5 minutes');

        return (string)$request->getUri();
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    public function handleUpload(UploadedFile $file)
    {
        $filename = uniqid() . "." . $file->getClientOriginalExtension();
        $path = "images/";
        $file->move($path, $filename);

        $sourceFile = $path . $filename;
        $bucket = 'hectors-lambda-test-ppictures';

        $this->s3->putObject([
            'Bucket'     => $bucket,
            'Key'        => $filename,
            'SourceFile' => $sourceFile
        ]);

        $dstBucket = $bucket . "-thumbnails";
        $dstKey    = "thumbnail-" . $filename;

        $url = $this->s3->getObjectUrl($dstBucket, $dstKey);

        unlink($sourceFile);
        return $url;
    }
}