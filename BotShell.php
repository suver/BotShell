<?php
namespace BotShell;

use BotShell\Di\DI;

/**
 * Путь с файлами Vile Elvis
 */
define("BS_PATH", dirname(__FILE__));

class BotShell
{

    private $shells = [];

    private static $instance;
    private static $di;

    public function __construct()
    {
        $this->addShell(BotShell::injection()->get('\BotShell\Console\Console'));
        $this->addShell(BotShell::injection()->get('\BotShell\Web\Web'));
    }

    public function __call($name, $arguments)
    {
        if (array_key_exists(strtolower($name), $this->shells)) {
            return $this->shells[$name];
        }
    }

    public function injection($injection = null)
    {
        if (!empty($injection)) {
            self::$di = $injection;
            return $this;
        } else if (empty(self::$di)) {
            self::$di = new DI();
        }
        return self::$di;
    }

    public static function shell()
    {
        if (empty(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function addShell(ShellInterface $shellObject)
    {
        $name = basename(str_replace('\\', '/', get_class($shellObject)));
        $this->shells[strtolower($name)] = $shellObject;
    }

    /**
     * Автолоадер
     */
    public static function autoload($className)
    {
        if (preg_match("#^BotShell#is", $className)) {
            $className = str_replace("BotShell\\", "", $className);
            $local_path = str_replace("\\", DIRECTORY_SEPARATOR, $className);
            // use include so that the error PHP file may appear
            if (file_exists(BS_PATH . DIRECTORY_SEPARATOR . $local_path . ".php")) {
                include_once(BS_PATH . DIRECTORY_SEPARATOR . $local_path . ".php");
            } else {
                throw new \Exception("Class {$className} not found");
            }
            return true;
        }
    }
}

spl_autoload_register(array('\BotShell\BotShell', 'autoload'));