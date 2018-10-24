# Phpuid

PHP Unique Identifier Generator，生成长ID和短ID两种。

## 长ID

### 特点
- id粗略有序
- 可反解
- 时间相关
- 可制造
- 不能保证绝对唯一，但重复的几率微乎其微，在可容忍范围内
- 多机器部署灵活、方便
- 参照Snowflake service from Twitter

### ID由四部分组成
- 最高位是符号位，始终为0
- 41位的时间序列(精确到毫秒，41位的长度可以使用69年)
- 10位的机器标识(包括数据中心5位 + 机器编号5位)（10位的长度最多支持部署1024个节点)
- 12位的毫秒内随机数（理论上12位支持每个节点每毫秒产生4096个ID序号)

### 缺点
毫秒内产生相同随机数会造成重复，虽然几率很小！因为id一般作为主键，写DB时如果有重复会写入失败，建议因为id重复写表失败的，重新生成新id重试写表一次（为了防止死循环，建议只重试一次，重试失败即抛错)。


## 短ID

### 特点
- id存储长度不超过32bit，以无符号int类型存储
- id表现长度为8~10位
- 支持扩展到8台机器
- id自增，步长随机
- 单机有序
- 可反解
- 时间不相关
- 不可制造
- 绝对唯一
- 多机器部署，需要多个数据库表支持，一机一表，一一对应

### ID由三部分组成
- msyql自增id：占19~27bit，单机支持(133,890,046 = 134217727 - 327681)个id
- 随机数：占2bit，相邻两个id的自增步长随机
- 机器编号：占3bit，支持扩展到8台机器

### 单机取值范围
最小值：1010000000000000001-00-001          (10485793)

最大值：111111111111111111111111111-00-001  (4294967265)

说明：1010000000000000001为最小值，是为了保证10进制id最短8位

### ID自增表
```
CREATE TABLE generate_uid_x (
    id int(10) unsigned NOT NULL auto_increment,
    stub char(1) NOT NULL default '',
    PRIMARY KEY  (id),
    UNIQUE KEY stub (stub)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
```

### SQL
```
REPLACE INTO generate_uid_x (stub) VALUES ('a');
SELECT LAST_INSERT_ID();
```

### 扩展性
假如id超过上限，只需修改业务id存储字段的数据类型为long型。
