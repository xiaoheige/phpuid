<?php
/**
 * 长UID生成器
 */
class LongUid
{
    // ID开始时间(毫秒) 2018-10-01 00:00:00
    const INIT_TIME = 1538323200000;
    
    //const time_bits       = 41;
    const datacenter_bits = 5;
    const machine_bits    = 5;
    const sequence_bits   = 12;
    
    /**
     * 生成ID
     * 
     * @param  int $machine 机器编号
     * @param  int $datacenter IDC编号
     * @return int
     */
    public static function generate($machine, $datacenter = 0) {
        if ($machine - intval($machine) != 0 || $datacenter - intval($datacenter) != 0) {
            return false;
        }
        
        $datacenter_max = -1 ^ (-1 << self::datacenter_bits);
        $machine_max    = -1 ^ (-1 << self::machine_bits);
        if ($machine < 0 || $machine > $machine_max
            || $datacenter < 0 || $datacenter > $datacenter_max) {
            return false;
        }
        
        $time_shift       = self::sequence_bits + self::machine_bits + self::datacenter_bits;
        $datacenter_shift = self::sequence_bits + self::machine_bits;
        $machine_shift    = self::sequence_bits;
        $sequence_max     = -1 ^ (-1 << self::sequence_bits);
        
        $time = floor(microtime(true) * 1000);
        
        return (($time - self::INIT_TIME) << $time_shift)
                    | ($datacenter << $datacenter_shift)
                    | ($machine << $machine_shift)
                    | mt_rand(0, $sequence_max);
    }
    
    /**
     * 根据ID，反解获得时间
     * 
     * @param  int $id
     * @return int
     */
    public static function get_time($id) {
        return ($id >> self::sequence_bits + self::machine_bits + self::datacenter_bits) + self::INIT_TIME;
    }
    
    /**
     * 根据ID，反解获得IDC编号
     * 
     * @param  int $id
     * @return int
     */
    public static function get_datacenter($id) {
        $shift = self::sequence_bits + self::machine_bits + self::datacenter_bits;
        return (($id >> $shift << $shift ^ $id) >> self::sequence_bits + self::machine_bits);
    }
    
    /**
     * 根据ID，反解获得机器编号
     * 
     * @param  int $id
     * @return int
     */
    public static function get_machine($id) {
        $shift = self::sequence_bits + self::machine_bits;
        return (($id >> $shift << $shift ^ $id) >> self::sequence_bits);
    }
    
}

