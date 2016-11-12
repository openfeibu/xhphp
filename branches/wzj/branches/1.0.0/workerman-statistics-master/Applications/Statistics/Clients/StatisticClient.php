<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
/**
 * 统计客户端
 * @author workerman.net
 */
namespace Clients;
use Clients\StatisticProtocol;

class StatisticClient
{
    /**
     * [module=>[interface=>time_start, interface=>time_start ...], module=>[interface=>time_start ..], ... ]
     * @var array
     */
    protected static $timeMap = array();
    
    /**
     * 模块接口上报消耗时间记时
     * @param string $module
     * @param string $interface
     * @return void
     */
    public static function tick($module = '', $interface = '')
    {
        return self::$timeMap[$module][$interface] = microtime(true);
    }
    
    /**
     * 上报统计数据
     * @param string $module
     * @param string $interface
     * @param bool $success
     * @param int $code
     * @param string $msg
     * @param string $report_address
     * @return boolean
     */
    public static function report($module, $interface, $success, $code, $msg, $report_address = '')
    {
        $report_address = $report_address ? $report_address : 'udp://127.0.0.1:55656';
        if(isset(self::$timeMap[$module][$interface]) && self::$timeMap[$module][$interface] > 0)
        {
            $time_start = self::$timeMap[$module][$interface];
            self::$timeMap[$module][$interface] = 0;
        }
        else if(isset(self::$timeMap['']['']) && self::$timeMap[''][''] > 0)
        {
            $time_start = self::$timeMap[''][''];
            self::$timeMap[''][''] = 0;
        }
        else
        {
            $time_start = microtime(true);
        }
         
        $cost_time = microtime(true) - $time_start;
        
        $bin_data = StatisticProtocol::encode($module, $interface, $cost_time, $success, $code, $msg);
  
        return self::sendData($report_address, $bin_data);
    }
    
    /**
     * 发送数据给统计系统
     * @param string $address
     * @param string $buffer
     * @return boolean
     */
    public static function sendData($address, $buffer)
    {
        $socket = stream_socket_client($address);
        if(!$socket)
        {
            return false;
        }
        return stream_socket_sendto($socket, $buffer) == strlen($buffer);
    }
    
}
