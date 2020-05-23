<?php


class store
{

    private static $infoMap = [];

    public static function add(int $fd, string $uid)
    {
        self::$infoMap[$fd] = $uid;
    }


    public static function remove(int $fd)
    {
        unset(self::$infoMap[$fd]);
    }

    public static function each(closure $fn)
    {
        foreach (self::$infoMap as $fd => $uid) {
            $fn($fd, $uid);
        }
    }

    public static function fds()
    {
        return array_keys(self::$infoMap);
    }

    public static function ids()
    {
        return array_unique(array_values(self::$infoMap));
    }
}
