from sys import argv
from struct import pack
key = '3+yI&*v/.+sI$6j+8Ix%-"\x12!)0\x120.*\x12W"6'
vals = [ 0x61050000, 0x67631205, 0x7b1d507f, 0x68764664, 0x6c1a5567, 0x6c1b2e6a, 0x03723608, 0x141a4719, 0x61684a64 ]
with open(argv[1], "rb") as f, open("bytes", "w") as w:
    buf = f.read()[::-1]
    for i in buf:
        w.write(hex(ord(i)) + ",")
    print("keybuf    equ      datastart + {}".format(str(buf.find(key))))
    for i,j in enumerate(vals):
        print("val{}off    equ      datastart + {}".format(i, str(buf.find(pack("<L", j)))))
