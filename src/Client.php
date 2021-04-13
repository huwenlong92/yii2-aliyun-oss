<?php


namespace larkit\oss;


use yii\base\Component;

/**
 * Class Client
 * @property Oss $oss
 * @property Sts $sts
 * @package larkit\oss
 */
class Client extends Component
{
    public $accessKeyId;
    public $accessKeySecret;

    public $bucket;
    public $regionId;

    public $endpoint;
    public $role_name;
    public $role_arn;


    public function getOss()
    {
        return new Oss($this);
    }

    public function getSts()
    {
        return new Sts($this);
    }

}