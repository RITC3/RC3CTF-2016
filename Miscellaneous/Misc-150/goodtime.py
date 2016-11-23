from SocketServer import TCPServer, BaseRequestHandler
from time import sleep
import socket

flag = "RC3-2016-itz-alw4yz-a-g00d-t1m1ng-@tt@ck"

class GoodTimeHandler(BaseRequestHandler):
    def handle(self):
        self.request.send("To have goodtime enter flag: ")
        resp = self.request.recv(1024).rstrip()
        if len(resp):
            for i,j in enumerate(resp):
                if j == flag[i]:
                    sleep(.25)
                else:
                    break
        else:
            self.request.send("Bruh. Send me SOMETHING!\n")
            return
        if flag == resp:
            self.request.send("Yup\n")
            self.request.send(flag+"\n")
        else:
            self.request.send("Nope\n")


class GoodTimeServer(TCPServer):
    def server_bind(self):
        self.socket.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
        self.socket.bind(self.server_address)


if __name__ == '__main__':
    s = GoodTimeServer(('0.0.0.0', 5866), GoodTimeHandler)
    s.serve_forever()

