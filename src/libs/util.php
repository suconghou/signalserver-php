<?php


class util
{

    public static function log(string $msg, string $level = 'INFO')
    {
        echo sprintf('%s : %s %s %s', $level, date('Y-m-d H:i:s'), $msg, PHP_EOL);
    }


    public static function errLog(Throwable $e)
    {
        echo PHP_EOL, $e, PHP_EOL;
    }

	public static function jsonPut($response, array $data)
	{
		$data = json_encode($data, (JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
		$response->header('Content-Type', 'application/json', false);
		return $response->end($data);
	}

}
