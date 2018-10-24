<?php
/**
 * 短UID生成器
 */
class ShortUid
{
    // ID基数
    const UID_BASE_NUM = 327680;
    
    //const mysqlid_bits = 27;
    const random_bits  = 2;
    const machine_bits = 3;
    
    /**
     * 生成ID
     * 
     * @param  int $type    用户类型 （1.普通用户 2.VIP ...）
     * @param  int $machine 机器编号 （0-7，支持最多8台机器部署）
     * @return int
     */
    public static function generate($type, $machine = 0) {
        if ($type - intval($type) != 0 || $machine - intval($machine) != 0) {
            return false;
        }
        
        $machine_max = -1 ^ (-1 << self::machine_bits);
        if ($machine < 0 || $machine > $machine_max) {
            return false;
        }
        
        // *** 注意：测试用随机数，生产用数据库 ***
        //$insert_id = db_query('replace into generate_uid (stub) values ("a")');
        $insert_id = mt_rand(100, 999);

        if (! is_numeric($insert_id) || $insert_id < 1) {
            return false;
        }
        
        $mysqlid_shift = self::random_bits + self::machine_bits;
        $random_shift  = self::machine_bits;
        $random_max    = -1 ^ (-1 << self::random_bits);
        
        $uid = (self::UID_BASE_NUM + $insert_id) << $mysqlid_shift
                | mt_rand(0, $random_max) << $random_shift
                | $machine;
        
        return $uid;
    }
    
    /**
     * 根据ID，反解获得数据库自增ID
     * 
     * @param  int $uid
     * @return int
     */
    public static function get_mysqlid($uid) {
        return ($uid >> self::random_bits + self::machine_bits) - self::UID_BASE_NUM;
    }

    /**
     * 根据ID，反解获得机器编号
     *
     * @param  int $uid
     * @return int
     */
    public static function get_machine($uid) {
        return $uid >> self::machine_bits << self::machine_bits ^ $uid;
    }
    
}

