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
     * @param UploadedFile $uploadedFile
     * @return string
     * @internal param UploadedFile $file
     */
    public function handleUpload(UploadedFile $uploadedFile)
    {
        $bucket = 'hectors-lambda-test-ppictures';

        $this->s3->putObject([
            'Bucket'     => $bucket,
            'Key'        => 'image.png',
            'SourceFile' => $uploadedFile
        ]);
        return '';
    }
}