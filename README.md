# ns.io

```
$server = Server::createWebsocket(12306);
//连接事件
$server->connection(function (Io $io) use ($server) {
    
});
//运行服务器
$server->run();
```

#class
###Io Api
绑定自定义事件
> on($event_name, $callback)

绑定断开连接事件
> disconnect($callback);

主动断开连接 
> close();

向客户端发起事件信号
> emit($event_name, $message);

加入一个组
> join($group_name);

离开一个组
> leave($group_name);

唯一标识
> getId();
