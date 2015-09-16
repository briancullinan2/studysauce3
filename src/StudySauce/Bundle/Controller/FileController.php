<?php

namespace StudySauce\Bundle\Controller;

use Aws\ElasticTranscoder\ElasticTranscoderClient;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use StudySauce\Bundle\Entity\File;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Aws\S3\S3Client;

/**
 * Class FileController
 * @package StudySauce\Bundle\Controller
 */
class FileController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function createAction(Request $request)
    {
        /** @var $user \StudySauce\Bundle\Entity\User */
        $user = $this->getUser();

        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        // Clean the fileName for security reasons
        $ext = substr($request->get('name'), strrpos($request->get('name'), '.'));
        $filename_a = preg_replace('/[^a-z0-9-]/i', '_', substr($request->get('name'), 0, strrpos($request->get('name'), '.')));
        $filename = $filename_a . $ext;

        // check database if file already exists
        /** @var File $file */
        $file = $user->getFiles()->filter(function (File $f) use($filename) {
                return $f->getFilename() == $filename;})->first();
        // Make sure the fileName is unique but only if chunking is disabled
        if ($request->get('chunks') < 2 && !empty($file))
        {
            $count = 1;
            while (!empty($file))
            {
                $file = $user->getFiles()->filter(function (File $f) use($filename_a, $ext, $count) {
                        return $f->getFilename() == $filename_a . '_' . $count . $ext;})->first();
                $count++;
            }

            $filename = $filename_a . '_' . $count . $ext;
        }


        // Look for the content type header
        if (isset($_SERVER['HTTP_CONTENT_TYPE']))
        {
            $contentType = $_SERVER['HTTP_CONTENT_TYPE'];
        }

        if (isset($_SERVER['CONTENT_TYPE']))
        {
            $contentType = $_SERVER['CONTENT_TYPE'];
        }

        // Handle non multi-part uploads older WebKit versions didn't support multi-part in HTML5
        // 1. Instantiate the client.
        $s3 = S3Client::factory([
                'key'    => 'AKIAIIESK4YKQTWYTU5Q',
                'secret' => '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',
            ]);

        // 2. Create a new multipart upload and get the upload ID.
        if($request->get('chunk') == 0)
        {
            $result = $s3->createMultipartUpload([
                    'Bucket'       => 'studysauce',
                    'Key'          => $filename,
                    'StorageClass' => 'REDUCED_REDUNDANCY',
                    'ACL'          => 'public-read',
                    'Metadata'     => []
                ]);
            $uploadId = $result['UploadId'];

            // save to database
            $file = new File();
            $file->setUser($user);
            $file->setFilename($filename);
            $file->setUploadId($uploadId);
            $file->setParts([]);
            $user->addFile($file);
            $orm->persist($file);
            $orm->flush();
        }
        if(empty($contentType))
            return self::returnTemporaryResponse(new JsonResponse(['error' => 'File could not be read.']));

        if (strpos($contentType, 'multipart') !== false)
        {
            if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name']))
            {
                $input = fopen($_FILES['file']['tmp_name'], 'rb');
            }
        }
        else
        {
            $input = fopen("php://input", 'rb');
        }

        if(empty($input))
            return self::returnTemporaryResponse(new JsonResponse(['error' => 'File could not be read.']));

        // 3. Upload the file in parts.
        $part = '';
        $parts = $file->getParts() ?: [];
        while (($buff = fread($input, 4096)))
            $part .= $buff;
        $result = $s3->uploadPart([
                'Bucket'     => 'studysauce',
                'Key'        => $filename,
                'UploadId'   => $file->getUploadId(),
                'PartNumber' => count($parts) + 1,
                'Body'       => $part, // same size as client javascript uploader
            ]);
        $parts[] = [
            'PartNumber' => count($parts) + 1,
            'ETag'       => $result['ETag'],
        ];
        fclose($input);

        // save parts to database so we can finish later
        $file->setParts($parts);
        $orm->merge($file);
        $orm->flush();

        // 4. Complete multipart upload.
        if ((isset($_GET['chunk']) && ($_GET['chunk'] + 1) == $_GET['chunks']) || (!isset($_GET['chunk'])))
        {
            $result = $s3->completeMultipartUpload([
                    'Bucket'   => 'studysauce',
                    'Key'      => $filename,
                    'UploadId' => $file->getUploadId(),
                    'Parts'    => $parts,
                ]);
            $url = $result['Location'];

            // save location to database
            $file->setUrl($url);
            $orm->merge($file);
            $orm->flush();

            // skip transcoder for non-video files
            if(!in_array($ext, ['.mov','.avi','.mpg','.mpeg','.wmv','.mp4','.webm','.flv','.m4v','.mkv','.ogv','.ogg','.rm','.rmvb','.m4v']))
                return self::returnTemporaryResponse(new JsonResponse(['src' => $url, 'fid' => $file->getId()]));

            // start the transcoder job
            $client = ElasticTranscoderClient::factory([
                    'key'    => 'AKIAIIESK4YKQTWYTU5Q',
                    'secret' => '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',
                    'region'  => 'us-west-2'
                ]);

            $result = $client->createJob([
                    // PipelineId is required
                    'PipelineId' => '1409877158303-aln6sp',
                    // Input is required
                    'Input' => [
                        'Key' => $filename
                    ],
                    'Output' => [
                        'Key' => substr($filename, 0, strrpos($filename, '.')) . '.webm',
                        'ThumbnailPattern' => substr($filename, 0, strrpos($filename, '.')) . '-{count}',
                        'PresetId' => '1409937723140-yb0pjg'
                    ]
                ]);

            if($result['Job']['Status'] == 'Submitted') {
                return self::returnTemporaryResponse(new JsonResponse(['status' => 'transcoding', 'fid' => $file->getId()]));
            }
        }
        return self::returnTemporaryResponse(new JsonResponse(['fid' => $file->getId()]));
    }

    /**
     * @param JsonResponse $response
     * @return JsonResponse
     */
    private static function returnTemporaryResponse(JsonResponse $response)
    {
        $response->headers->set('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT', true);
        $response->headers->set('Last-Modified', gmdate("D, d M Y H:i:s") . ' GMT', true);
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate post-check=0, pre-check=0', true);
        $response->headers->set('Pragma', 'no-cache', true);
        return $response;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function checkStatusAction(Request $request)
    {
        // 1. Instantiate the client.
        /** @var $user \StudySauce\Bundle\Entity\User */
        $user = $this->getUser();
        
        /** @var File $file */
        $file = $user->getFiles()->filter(function (File $f) use ($request) { return $f->getId() == $request->get('fid');})->first();
        try
        {
            $s3 = \Aws\S3\S3Client::factory([
                    'key'    => 'AKIAIIESK4YKQTWYTU5Q',
                    'secret' => '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',
                ]);

            // 2. Check if object exists in bucket
            $s3->getObject([
                    'Bucket'       => 'studysauce',
                    'Key'          => $file->getFilename()
                ]);
        }
        catch(\Exception $ex)
        {
            return new JsonResponse('error');
        }

        try
        {
            $result = $s3->getObject([
                    'Bucket'       => 'studysauce',
                    'Key'          => substr($file->getFilename(), 0, strrpos($file->getFilename(), '.')) . '.webm'
                ]);

            $date = $result['LastModified'];
            if($date)
                return new JsonResponse([
                        'thumb' => substr($file->getFilename(), 0, strrpos($file->getFilename(), '.')) . '-00001.png',
                        'src' => substr($file->getFilename(), 0, strrpos($file->getFilename(), '.')) . '.webm'
                    ]);
        }
        catch(\Exception $ex)
        {
            // just ignore because it isn't done generating
        }
        return new JsonResponse(true);
    }

    /**
     * @param $_user
     * @internal param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function partnerAction($_user)
    {
        /** @var $userManager UserManager */
        $userManager = $this->get('fos_user.user_manager');

        /** @var $user User */
        $user = $userManager->findUserBy(['id' => intval($_user)]);

        return $this->render('StudySauceBundle:Partner:uploads.html.php', ['user' => $user]);
    }

    /**
     * @param $name
     * @param $filename
     * @param $new_w
     * @param $new_h
     */
    function createThumb($name,$filename,$new_w,$new_h){
        $system=explode('.',$name);
        if (preg_match('/jpg|jpeg/',$system[1])){
            $src_img=imagecreatefromjpeg($name);
        }
        if (preg_match('/png/',$system[1])){
            $src_img=imagecreatefrompng($name);
        }
        $old_x=imageSX($src_img);
        $old_y=imageSY($src_img);
        if ($old_x > $old_y) {
            $thumb_w=$new_w;
            $thumb_h=$old_y*($new_h/$old_x);
        }
        if ($old_x < $old_y) {
            $thumb_w=$old_x*($new_w/$old_y);
            $thumb_h=$new_h;
        }
        if ($old_x == $old_y) {
            $thumb_w=$new_w;
            $thumb_h=$new_h;
        }
        $dst_img=ImageCreateTrueColor($thumb_w,$thumb_h);
        imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y);
        if (preg_match("/png/",$system[1])){
            imagepng($dst_img,$filename);
        } else {
            imagejpeg($dst_img,$filename);
        }
        imagedestroy($dst_img);
        imagedestroy($src_img);
    }
}
