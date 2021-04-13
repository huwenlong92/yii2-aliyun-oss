<?php

namespace larkit\oss;

class Sts extends Base
{
    public function config($path = '')
    {
        if (empty($path)) {
            $path = 'tmp/';
        }
        $now = time();
        $expire = 30; //设置该policy超时时间是10s. 即这个policy过了这个有效时间，将不能访问
        $end = $now + $expire;
        $policy = [
            'expiration' => $this->gmt_iso8601($end),
            'conditions' => [
                //最大文件大小.用户可以自己设置
                [
                    'content-length-range',
                    0,
                    1048576000
                ],
                ["bucket" => $this->client->bucket],
                ["starts-with", "\$key", $path]
            ]
        ];
        $policy = json_encode($policy, JSON_UNESCAPED_UNICODE);
        $base64_policy = base64_encode($policy);
        $string_to_sign = $base64_policy;
        $signature = base64_encode(hash_hmac('sha1', $string_to_sign, $this->client->accessKeySecret, true));

        return [
            'accessKeyId' => $this->client->accessKeyId,
            'RegionId' => $this->client->regionId,
            'endpoint' => $this->client->endpoint,
            'Bucket' => $this->client->bucket,
            'Signature' => $signature,
            'policy' => $base64_policy,
            'expire' => $end,
            'path' => $path,
        ];

    }

    private function gmt_iso8601($time)
    {
        $dtStr = date("c", $time);
        $mydatetime = new \DateTime($dtStr);
        $expiration = $mydatetime->format(\DateTime::ISO8601);
        $pos = strpos($expiration, '+');
        $expiration = substr($expiration, 0, $pos);
        return $expiration . "Z";
    }
}