#include <stdio.h>
#include <stdlib.h>
#include <string.h>

char *flag = "\x36\x22\x57\x12\x2a\x2e\x30\x12\x30\x29\x21\x12\x22\x2d\x25\x78\x49\x38\x2b\x6a\x36\x24\x49\x73\x2b\x2e\x2f\x76\x2a\x26\x49\x79\x2b\x33";
unsigned long val5 = 0x67551a6c;
unsigned long val6 = 0x6a2e1b6c;
unsigned long val7 = 0x08367203;


void enc(char *flag){
    char key[5];
    unsigned long val3 = 0x7f501d7b;
    unsigned char *val4 = "hvFd";
    unsigned long val8 = 0x19471a14;
    key[0] = 'd';
    key[1] = 'a';
    key[2] = 'd';
    key[3] = '?';
    key[4] = 0;
    int len = strlen(flag);
    for (int i=0;i<len;i++)
        flag[i] ^= key[i%strlen(key)];
}

int main(int argc, char *argv[])
{
    unsigned long val1 = 0x00000561;
    unsigned long val2 = 0x05126367;
    unsigned char *val9 = "ahJd";
    char *buf = malloc(val1);
    strcpy(buf, argv[1]);
    enc(buf);
    if (!memcmp(flag,buf,35))
        puts("gottem");
    free(buf);
    return 0;
}
