<?php

namespace dux;

error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', '1');

/**
 * 最低PHP版本要求
 */
const PHP_REQUIRED = '7.1.0';

class Start {

    private function __construct() {
    }

    private function __destruct() {
    }

    private function __clone() {
    }

    public static $_routes = [];


    /**
     * 运行框架
     */
    public static function run() {
        if (!defined('IS_CLI')) define('IS_CLI', preg_match("/cli/i", php_sapi_name()) ? true : false);
        if(!IS_CLI) {
            self::environment();
        }
        self::definitions();
        self::loadFile();
        self::loadConfig();
        self::loadFunCom();
        self::loadClass();
        self::registerCom();
        self::route();
        self::start();
    }

    /**
     * 定义常量
     */
    protected static function definitions() {
        if (!defined('ROOT_PATH')) {
            echo 'Please define ROOT_PATH constants';
            exit;
        }
        if (!defined('VERSION')) define('VERSION', '1.1.15');
        if (!defined('VERSION_DATE')) define('VERSION_DATE', '20190413');
        if (!defined('URL')) define('URL', $_SERVER['REQUEST_URI']);
        if (!defined('START_TIME')) define('START_TIME', microtime());
        if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
        if (!defined('CORE_PATH')) define('CORE_PATH', __DIR__ . '/');
        if (!defined('DATA_PATH')) define('DATA_PATH', ROOT_PATH . 'data/');
        if (!defined('APP_PATH')) define('APP_PATH', ROOT_PATH . 'app/');
        if (!defined('PACK_PATH')) define('PACK_PATH', CORE_PATH . 'package/');
        if (!defined('ROOT_URL')) define('ROOT_URL', str_replace('\\', '/', rtrim(dirname($_SERVER["SCRIPT_NAME"]), '\\/')));
        if (!defined('ROOT_SCRIPT')) define('ROOT_SCRIPT', str_replace('\\', '/', rtrim($_SERVER["SCRIPT_NAME"], '\\/')));
        $urlHead = ($_SERVER['HTTPS'] <> "on") ? 'http' : 'https';
        $urlHead .= '://' . $_SERVER["HTTP_HOST"];
        if (!defined('DOMAIN')) define('DOMAIN', $urlHead);
        $urlHead = 'http://' . $_SERVER["HTTP_HOST"];
        if (!defined('DOMAIN_HTTP')) define('DOMAIN_HTTP', $urlHead);
    }

    /**
     * 设置环境
     */
    protected static function environment() {
        //设置跨域
        header('Access-Control-Allow-Origin:' . $_SERVER["HTTP_ORIGIN"]);
        header('Access-Control-Allow-Headers:' . $_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"]);
        //判断PHP版本
        if (version_compare(PHP_VERSION, PHP_REQUIRED, '<')) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 500 PHP_VERSION');
            echo 'Can only run in PHP is greater than 7.1';
            exit;
        }
        //兼容环境信息
        date_default_timezone_set('PRC');
        if (!isset($_SERVER['HTTP_REFERER'])) {
            $_SERVER['HTTP_REFERER'] = '';
        }
        if (!isset($_SERVER['SERVER_PROTOCOL'])
            || ($_SERVER['SERVER_PROTOCOL'] != 'HTTP/1.0' && $_SERVER['SERVER_PROTOCOL'] != 'HTTP/1.1')
        ) {
            $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.0';
        }
        if (isset($_SERVER['HTTP_HOST'])) {
            $_SERVER['HTTP_HOST'] = strtolower($_SERVER['HTTP_HOST']);
        } else {
            $_SERVER['HTTP_HOST'] = '';
        }
        if (!isset($_SERVER['REQUEST_URI'])) {
            if (isset($_SERVER['argv'])) {
                $uri = $_SERVER['PHP_SELF'] . '?' . $_SERVER['argv'][0];
            } else {
                $uri = $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
            }
            $_SERVER['REQUEST_URI'] = $uri;
        }
        if (!function_exists('getallheaders')) {
            function getallheaders() {
                foreach ($_SERVER as $name => $value) {
                    if (substr($name, 0, 5) == 'HTTP_') {
                        $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                    }
                }
                return $headers;
            }
        }

    }

    /**
     * 加载核心文件
     */
    protected static function loadFile() {
        if(IS_CLI) {
            $params = getopt('u:m:');
            $_SERVER['REQUEST_URI'] = $params['u'];
            if($params['m']) {
                if (!defined('SYSTEM_MODEL')) define('SYSTEM_MODEL', $params['m']);
            }
        }
    }

    /**
     * 加载配置
     */
    protected static function loadConfig() {
        $config = require(DATA_PATH . 'config/global.php');
        \dux\Config::set($config);
    }

    /**
     * 加载核心类
     */
    protected static function loadClass() {
    }

    /**
     * 注册核心方法
     */
    protected static function registerCom() {

    }

    /**
     * 加载公共函数库
     */
    protected static function loadFunCom() {
        require __DIR__ . '/kernel/Function.php';
    }

    /**
     * 注册路由
     */
    protected static function route() {
    }

    /**
     * 启动框架
     */
    protected static function start() {
        if (strtolower($_SERVER['REQUEST_METHOD']) == 'options') {
            Dux::header(204);
        }
        $dux = new \dux\Engine();
        $dux->run();
    }

}