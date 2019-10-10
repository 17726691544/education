<?php

namespace app\common\service;

use OSS\OssClient;
use OSS\Core\OssException;
class OssService
{
    const ACCESS_KEY_ID = 'LTAIt7lW0rdZzEvT';
    const ACCESS_KEY_SECRET = 'Pm4jWGGeLPkjZnWuXywdQEnaP3DOBi';
    const ENDPOINT = 'https://oss-cn-chengdu.aliyuncs.com/';
    const VISIT_DOMAIN = 'https://jiaoyu2019.oss-cn-chengdu.aliyuncs.com/';
    const BUCKET = 'jiaoyu2019';
    const PROJECT = 'education';

    public static function uploadFile($localPath,$saveName,$dir = '') {
        if ($dir) {
            $saveName = self::PROJECT . '/' . $dir . '/' . $saveName;
        } else {
            $saveName = self::PROJECT . '/' . $saveName;
        }

        try{
            $ossClient = new OssClient(self::ACCESS_KEY_ID, self::ACCESS_KEY_SECRET, self::ENDPOINT);
            $ossClient->uploadFile(self::BUCKET, $saveName, $localPath);
        } catch(OssException $e) {
            return false;
        }

        return self::VISIT_DOMAIN . $saveName;
    }

}