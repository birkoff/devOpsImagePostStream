<?php

namespace AppBundle\Service;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Aws\Sdk;

date_default_timezone_set('UTC');

class S3Wrapper implements StorageInterface
{
    /** @var \Aws\S3\S3Client  */
    private $s3;

    /** @var  String */
    private $bucket;

    public function  __construct($bucket)
    {
        $this->bucket = $bucket;

        $sdk = new Sdk([
            'profile'  => 'birkoff',
            'region'   => 'eu-west-1',
            'version'  => 'latest'
        ]);

        $this->s3 = $sdk->createS3();
    }

    /**
     * @param File $file
     * @param $filename
     * @return string
     * @throws \Exception
     */
    public function save(File $file, $filename)
    {
        $sourceFile = $file->getPath() . $file->getFilename();

        $result = $this->s3->putObject([
            'Bucket'     => $this->bucket,
            'Key'        => $filename,
            'SourceFile' => $sourceFile
        ]);

        if(!$result || !isset($result['ObjectURL'])) {
            throw new \Exception('Error publishing uploading object to S3');
        }

        //return $result['ObjectURL'];

        $dstBucket = $this->bucket . "-thumbnails";
        $dstKey    = "thumbnail-" . $filename;

        $url = $this->s3->getObjectUrl($dstBucket, $dstKey);

        unlink($sourceFile);
        return $url;
    }
}