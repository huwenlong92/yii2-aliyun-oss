<?php


namespace larkit\oss;


use common\components\exceptions\Exception;
use OSS\OssClient;

/**
 * Class Oss
 * @property OssClient $oss
 * @package larkit\oss
 */
class Oss extends Base
{
    protected $oss;

    public function __construct(Client $client)
    {
        parent::__construct($client);
        $this->oss = new OssClient($this->client->accessKeyId, $this->client->accessKeySecret, $this->client->endpoint);
    }


    /**
     * 使用阿里云oss上传文件
     * @param $object // 保存到阿里云oss的文件名
     * @param $filepath // 文件在本地的绝对路径
     * @return bool     上传是否成功
     * @throws \Exception
     */
    public function upload($object, $filepath)
    {
        $bucket = $this->client->bucket;
        $endpoint = $this->client->endpoint;
        try {
            if ($this->oss->uploadFile($bucket, $object, $filepath)) {  //调用uploadFile方法把服务器文件上传到阿里云oss
                return sprintf("http://%s.%s/%s", $bucket, $endpoint, $object);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function list_dir($prefix, $offset = '', $limit = 100, $delimiter = '/')
    {
        $data = [
            'code' => 20000,
            'msg' => 'success',
            'data' => []
        ];
        $dir = [];
        $options = [
            'delimiter' => $delimiter,
            'prefix' => $prefix,
            'max-keys' => $limit,
            'marker' => $offset,
        ];
        try {
            $res = $this->oss->listObjects($this->bucket, $options);
            $nextMarker = $res->getNextMarker();
            if (!empty($listPrefix = $res->getPrefixList())) {
                foreach ($listPrefix as $prefixInfo) {
                    array_push($dir, $prefixInfo->getPrefix());
                }
            }
            $data['data'] = [
                'list' => $dir,
                'offset' => $nextMarker
            ];
        } catch (\Exception $e) {
            $data['code'] = $e->getCode() ?: 4000;
            $data['msg'] = $e->getMessage();
        }
        return $data;
    }

    public function list_file($prefix, $offset = '', $limit = 100, $delimiter = '')
    {
        $data = [
            'code' => 20000,
            'msg' => 'success',
            'data' => []
        ];
        $files = [];
        $options = [
            'delimiter' => $delimiter,
            'prefix' => $prefix,
            'max-keys' => $limit,
            'marker' => $offset,
        ];
        try {
            $res = $this->oss->listObjects($this->bucket, $options);
            $nextMarker = $res->getNextMarker();
            if (!empty($listObject = $res->getObjectList())) {
                foreach ($listObject as $objectInfo) {
                    array_push($files, $objectInfo->getKey());
                }
            }
            $data['data'] = [
                'list' => $files,
                'offset' => $nextMarker
            ];
        } catch (\Exception $e) {
            $data['code'] = $e->getCode() ?: 4000;
            $data['msg'] = $e->getMessage();
        }
        return $data;
    }
}