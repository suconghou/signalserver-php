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

    public static function fds():array
    {
        return array_keys(self::$infoMap);
    }

    public static function ids():array
    {
        return array_unique(array_values(self::$infoMap));
    }

    public static function uids(string $uid):array
    {
        $fds=[];
        foreach(self::$infoMap as $fd => $id)
        {
            if($id==$uid)
            {
                $fds[]=$fd;
            }
        }
        return $fds;
    }

}
