<html>
<head>
    <meta charset="UTF-8">
    <title>Web sockets test</title>
    <script src="{{ asset('/js/jquery2.1.1.min.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
        var ws;
        function ToggleConnectionClicked() {          
                try {
                    ws = new WebSocket("ws://127.0.0.1:2000");//连接服务器        
                    ws.onopen = function(event){alert("已经与服务器建立了连接\r\n当前连接状态："+this.readyState);};
                    ws.onmessage = function(event){alert("接收到服务器发送的数据：\r\n"+event.data);};
                    ws.onclose = function(event){alert("已经与服务器断开连接\r\n当前连接状态："+this.readyState);};
                    ws.onerror = function(event){alert("WebSocket异常！");};
                } catch (ex) {
                    alert(ex.message);      
                }
        };
 
        function SendData() {
            try{
                var content = document.getElementById("content").value;
                if(content){
                    ws.send(content);
                }
                
            }catch(ex){
                alert(ex.message);
            }
        };
 
        function seestate(){
            alert(ws.readyState);
        }
       
    </script>
</head>
<body>
   <button id='ToggleConnection' type="button" onclick='ToggleConnectionClicked();'>连接服务器</button><br /><br />
   <textarea id="content" ></textarea>
    <button id='ToggleConnection' type="button" onclick='SendData();'>发送我的名字：beston</button><br /><br />
    <button id='ToggleConnection' type="button" onclick='seestate();'>查看状态</button><br /><br />

</body>
</html>