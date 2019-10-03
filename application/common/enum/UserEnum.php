<?php


namespace app\common\enum;


class UserEnum
{
    // 用户类型 1：普通会员 2：老师
    const USER_TYPE_GEN = 1;
    const USER_TYPE_TEA = 2;

    //用户状态 1：待审核 2:审核通过 3：审核失败 4：黑名单
    const USER_STATUS_AUDIT = 1;
    const USER_STATUS_AUDITSUCEE = 2;
    const USER_STATUS_AUDITFAIL = 3;
    const USER_STATUS_HITLIST = 4;

    //用户等级
    const USER_LEVEL_1 = 1;
}