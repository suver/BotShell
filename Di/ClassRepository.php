<?php
namespace BotShell\Di;


class ClassRepository
{

    private static $storage;

    public static function init()
    {
//        $classes = get_declared_classes();
//        foreach ($classes as $class) {
//            $name = str_replace('\\\\', '\\', $class);
//            self::$storage[$name] = $class;
//        }
//        var_dump(self::$storage);
    }

    public static function set($dependence, $inject)
    {
        self::$storage[$dependence] = $inject;
    }

    public static function get($dependence)
    {
//        var_dump(self::$storage, $dependence, self::$storage[$dependence]);
        return self::$storage[$dependence];
    }
}