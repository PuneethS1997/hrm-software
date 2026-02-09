use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

require 'vendor/autoload.php';

$server = IoServer::factory(
 new HttpServer(
   new WsServer(
     new ChatServer()
   )
 ),
 8080
);

$server->run();
