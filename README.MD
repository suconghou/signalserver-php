## 信令服务器

signalserver in php , powered by swoole

works with fastloadjs

**dev in docker** 

```
docker run --rm -it -p 9090:9092 -v "$PWD":/data suconghou/php:swoole sh  
```

similar project

- https://github.com/suconghou/signalserver

### systemd service

`/etc/systemd/system/signalserver.service`
```
[Unit]
Description=signalserver service
After=network.target network-online.target

[Service]
Restart=on-failure
TimeoutStartSec=0
ExecStart=/usr/local/bin/signalserver

[Install]
WantedBy=multi-user.target

```