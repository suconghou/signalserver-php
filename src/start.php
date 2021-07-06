<?php

require 'libs/util.php';
require 'libs/store.php';
require 'libs/route.php';
require 'libs/request.php';

class app
{

    private $server;

    function __construct(string $host, int $port)
    {
        $this->server = new Swoole\WebSocket\Server($host, $port, SWOOLE_BASE);
    }

    private function open(Swoole\Websocket\Server $server, $req)
    {
        $fd = $req->fd;
        $path_info = $req->server['path_info'];
        if (preg_match('/uid\/([\w\-]{36})$/', $path_info, $matches)) {
            $uid = $matches[1];
        } else {
            $server->disconnect($fd, 1000, 'uid error');
            return;
        }
        $uids = store::ids();
        $this->json($fd, ['event' => 'init', 'ids' => $uids]);
        $this->broadcast(['event' => 'online', 'id' => $uid]);
        store::add($fd, $uid);
        util::log("connection open: {$fd} {$uid}");
    }

    private function message(Swoole\Websocket\Server $server, $frame)
    {
        $data = json_decode($frame->data, true, 32, JSON_THROW_ON_ERROR);
        $to = $data['to'] ?? '';
        if ($to) {
            $fds = store::uids($to);
            foreach ($fds as $fd) {
                $this->server->push($fd, $frame->data);
            }
        } else {
            util::log("can not broadcast");
        }
    }

    private function close(Swoole\Websocket\Server $server, $fd)
    {
        util::log("connection close: {$fd}");
        store::remove($fd);
    }

    private function request(Swoole\Websocket\Server $server, $request, $response)
    {
        route::run($server, $request, $response);
    }

    public function run()
    {
        $this->server->on('open', function (Swoole\Websocket\Server $server, $req) {
            try {
                $this->open($server, $req);
            } catch (Throwable $e) {
                util::errLog($e);
            }
        });
        $this->server->on('message', function (Swoole\Websocket\Server $server, $frame) {
            try {
                $this->message($server, $frame);
            } catch (Throwable $e) {
                util::errLog($e);
            }
        });
        $this->server->on('close', function (Swoole\Websocket\Server $server, $fd) {
            try {
                $this->close($server, $fd);
            } catch (Throwable $e) {
                util::errLog($e);
            }
        });
        $this->server->on('request', function ($request, $response) {
            try {
                $this->request($this->server, $request, $response);
            } catch (Throwable $e) {
                util::errLog($e);
            }
        });
        $this->server->on('WorkerStart', function (Swoole\Websocket\Server $server) {
        });
        $this->server->on('start', function (Swoole\Websocket\Server $server) {
            try {
                request::regist();
                echo "Swoole server is started at {$server->host}:{$server->port}\n";
            } catch (Throwable $e) {
                util::errLog($e);
            }
        });
        $this->server->start();
    }

    private function json(int $fd, array $data)
    {
        return $this->server->push($fd, json_encode($data, (JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)));
    }

    private function broadcast(array $data)
    {
        $data = json_encode($data, (JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        store::each(function (int $fd) use (&$data) {
            $this->server->push($fd, $data);
        });
    }
}


$host = '0.0.0.0';
$port = getenv('PORT') ?: 9092;

(new app($host, $port))->run();
