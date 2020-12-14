<?php

use \Output\Output;
use \Authentication\Authentication;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

/**
* Upload image to AWS and return url file
*
* @return json - Image file data
*
**/

$app->post('/upload_image', function ($request, $response, array $args) {
  $Authentication = new Authentication($this->db, $request->getHeader('Authorization'));
  $Output = new Output();

  if (!$Authentication->isValid()) {
    return $Output->response($response, 403, 'NOT_ATHENTICATED');
  }

  $aws = $this->get('settings')['aws'];

  $bucket = $aws['bucket'];
  $credentials = new Aws\Credentials\Credentials($aws['key'], $aws['secret']);

  $s3 = new S3Client([
    'version' => 'latest',
    'region'  => $aws['region'],
    'credentials' => $credentials
  ]);

  $uploadedFiles = $request->getUploadedFiles();

  $filename = rand(100, 100000) . '' . time() . '.jpg';

  try {
    $result = $s3->putObject([
      'Bucket' => $bucket,
      'SourceFile'  => $uploadedFiles['file']->file,
      'Key' => $filename,
      'ContentType' => 'image/jpg',
      'ACL' => 'public-read'
    ]);

    return $Output->response($response, 200, array('url' => $result['ObjectURL']));
  } catch (S3Exception $e) {
    return $Output->response($response, 400, array('error' => $e->getMessage() . PHP_EOL));
  }
});
