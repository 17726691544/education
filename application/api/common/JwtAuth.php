<?php


namespace app\api\common;


use app\common\exception\BusinessBaseException;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
use think\Exception;

class JwtAuth
{
    private static $instance;
    private $secret = 'Aa123123456';
    private $expiresAt = 86400;
    private $tokenData;


    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 创建token
     * @param array $params
     * @return string
     * @throws Exception
     */
    public function tokenEncode($params = [])
    {
        if (!is_array($params)) {
            return false;
        }
        $uniqId = md5(uniqid(microtime(true), true));

        try {
            $signer = new Sha256();
            $time = time();
            $builder = new Builder();
            $builder->identifiedBy($uniqId, true) //令牌唯一标识，防止重放
            ->issuedAt($time) //令牌生成时间
            ->expiresAt($time + $this->expiresAt); //过期时间
            //封装自定义数据
            foreach ($params as $key => $value) {
                $builder->withClaim((string)$key, $value);
            }
            return (string)($builder->getToken($signer, new Key($this->secret)));
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 解析token
     * @param $token
     * @throws \Exception
     */
    public function tokenDecode($token)
    {
        if (!$token) {
            throw new BusinessBaseException('无效的令牌');
        }
        try {
            $signer = new Sha256();
            //解析token
            $parse = (new Parser())->parse(trim((string)$token));
            //验证token合法性
            if (!$parse->verify($signer, $this->secret)) {
                throw new BusinessBaseException('错误的令牌');
            }
            //验证是否已经过期
            if ($parse->isExpired()) {
                throw new BusinessBaseException('令牌已过期');
            }
            //获取数据
            $this->tokenData = $parse->getClaims();
            return true;
        } catch (\Exception $e) {
            return false;
        }

    }

    /**
     * 获取数据
     * @param null $key
     * @return |null
     */
    public function data($key = null)
    {
        $uData = $this->tokenData;
        if (empty($uData)) {
            return null;
        }
        if (empty($key)) {
            return $uData;
        }
        if (array_key_exists($key, $uData)) {
            return $uData[$key];
        }
    }

    /**
     * 获取uid
     * @return mixed
     */
    public function getUid()
    {
        return $this->data('uid');
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }

}