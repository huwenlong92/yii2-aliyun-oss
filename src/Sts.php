<?php

namespace larkit\oss;
include_once 'lib/sts-server/aliyun-php-sdk-core/Config.php';

use Sts\Request\V20150401 as OssSts;

class Sts extends Base
{
    public function RamSts()
    {
        $expired = 60 * 15;
        $iClientProfile = \DefaultProfile::getProfile($this->client->regionId, $this->client->accessKeyId, $this->client->accessKeySecret);
        $client = new \DefaultAcsClient($iClientProfile);
        $request = new OssSts\AssumeRoleRequest();
//            $request->setRoleSessionName('test');
//            $request->setRoleArn('acs:ram::1854008084453692:role/test');
        $request->setRoleSessionName($this->client->role_name);
        $request->setRoleArn($this->client->role_arn);
        $request->setDurationSeconds($expired);
        try {
            $response = $client->getAcsResponse($request);
        } catch (\ServerException $e) {
            throw $e;
        } catch (\ClientException $e) {
            throw $e;
        }
        $sts = [
            'AccessKeyId' => $response->Credentials->AccessKeyId,
            'AccessKeySecret' => $response->Credentials->AccessKeySecret,
            'Expiration' => $response->Credentials->Expiration,
            'SecurityToken' => $response->Credentials->SecurityToken,
            'RegionId' => $this->client->regionId,
            'endpoint' => $this->client->endpoint,
            'Bucket' => $this->client->bucket
        ];
        return $sts;
    }
}