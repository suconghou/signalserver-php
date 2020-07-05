## 信令服务器


**dev in docker** 

```
docker run --rm -it -p 9090:9092 -v "$PWD":/data suconghou/php:swoole sh  
```

written in php , powered by swoole

works with fastloadjs

信令服务器

启动 -> 链接WS -> 发送本地OFFER/ICE

在一个区域里广播,

使用uuid链接.

用户连接 -> 
自己收到 init 
{
    id,
    ids
}
告诉自己的ID和所有在线的ID

使用sessionStorage 鉴别唯一客户端. 用户断线重连的情况

自己应初始化,这些ID的链接,等待他们主动联系自己

其他已在线人员收到有人上线消息
{
    online,
    id: 上线人的ID
}

## 消息类别

**1. 我方上线时初始化消息**

```
{
    "event":"init"
}
```

**2.广播我方上线**

```
{
    "event":"online"
}
```


**3.向某个ID发送offer**



