<?php

class route
{

	private static $routes = [];

	private static $notfound;


	public static function get(string $regex, Closure $fn)
	{
		return self::add($regex, $fn, ['GET']);
	}


	public static function post(string $regex, Closure $fn)
	{
		return self::add($regex, $fn, ['POST']);
	}


	public static function put(string $regex, Closure $fn)
	{
		return self::add($regex, $fn, ['PUT']);
	}


	public static function delete(string $regex, Closure $fn)
	{
		return self::add($regex, $fn, ['DELETE']);
	}


	public static function head(string $regex, Closure $fn)
	{
		return self::add($regex, $fn, ['HEAD']);
	}


	public static function any(string $regex, Closure $fn, array $methods = ['GET', 'POST', 'PUT', 'DELETE', 'HEAD'])
	{
		return self::add($regex, $fn, $methods);
	}


	public static function add(string $regex, Closure $fn, array $methods)
	{
		self::$routes[] =
			[
				$regex,
				$fn,
				$methods,
			];
	}


	public static function notfound(Closure $fn)
	{
		self::$notfound = $fn;
	}


	public static function run(Swoole\Websocket\Server $server, $request, $response)
	{
		list('request_uri' => $uri, 'request_method' => $m) = $request->server;
		$uri = '/' . implode('/', array_values(array_filter(explode('/', $uri, 9), 'strlen')));
		$ret = self::match($uri, $m);
		if ($ret) {
			list($url, $params, $fn) = $ret;
			return self::call($fn, [$server, $request, $response], $url, $params);
		}

		if (!self::$notfound) {
			self::$notfound = function (Swoole\Websocket\Server $server, $request, $response) {
				$response->status(404);
				$response->end('Not Found');
			};
		}
		return self::call(self::$notfound, [$server, $request, $response], $uri, []);
	}


	private static function match(string $uri, string $m)
	{
		foreach (self::$routes as $i => list($regex, $fn, $methods)) {
			if (in_array($m, $methods, true) && preg_match("/^{$regex}$/", $uri, $matches)) {
				$url = array_shift($matches);
				return [
					$url,
					$matches,
					$fn,
				];
			}
		}

		return false;
	}


	private static function call(Closure $fn, array $ctx, string $url, array $params)
	{
		return call_user_func_array($fn, array_merge($ctx, $params));
	}
}
