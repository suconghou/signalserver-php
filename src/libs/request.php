<?php

class request
{


	public static function regist()
	{
		route::get(
			'\/api\/status',
			function ($server, $request, $response) {
				self::status($request, $response);
			}
		);
	}


	private static function status($request, $response)
	{
		$data = [
			'code' => 0,
			'msg'  => 'ok',
		];
		return util::jsonPut($response, $data);
	}
}
