<?php

namespace app\common\service;

use OSS\OssClient;
use OSS\Core\OssException;
class OssService
{
    const ACCESS_KEY_ID = 'LTAIeYRciWnQkwz1';
    const ACCESS_KEY_SECRET = 'kZ1ao8U4byg2mfb4SC7JqSClHPqFbm';
    const ENDPOINT = 'https://oss-cn-shanghai.aliyuncs.com/';
    const VISIT_DOMAIN = 'https://musongtest.oss-cn-shanghai.aliyuncs.com/';
    const BUCKET = 'musongtest';
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