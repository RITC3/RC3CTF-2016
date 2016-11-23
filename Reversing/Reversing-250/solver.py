from os import system
from sys import argv, exit

def derive_password():
    b = [0x0e, 0x06, 0x05, 0x08, 0x07, 0x0e, 0x44, 0x06, 0x1b, 0x44, 0x0b, 0x1c, 0x1a, 0x1d]
    return ''.join([chr(i ^ 0x69) for i in b ])

def decrypt(f):
    with open(f, 'rb') as enc:
        b = bytearray(enc.read())
    for i in xrange(len(b)-2, -1, -1):
        b[i] = (b[i] + b[i+1]) & 0xff
    with open("flag.zip", 'wb') as o:
        o.write(b)

def unzip(f, p):
    system("7za e -yp'{}' {}".format(p, f))

if __name__ == '__main__':
    if len(argv) != 2:
        print("{} <file to decrypt/unzip>".format(argv[0]))
        exit()
    decrypt(argv[1])
    print(derive_password())
    unzip("flag.zip", derive_password())
    system("cat flag.txt")
