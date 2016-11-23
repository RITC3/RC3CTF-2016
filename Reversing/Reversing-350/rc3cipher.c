#include <stdio.h>
#include <string.h>
#include <stdlib.h>

char functionthree(char* str){
    char maxc = -1;
    for (int i=0;i<strlen(str);++i){
        if (str[i] > maxc)
            maxc = str[i];
    }
    return maxc;
}

void functionone(unsigned char *ksa, unsigned char *k, int ksalen){
    int tmp, j=0;
    for (int i=ksalen-1;i>=0;i--)
        ksa[ksalen-i-1] = (unsigned char)i;
    for (int i=0;i<ksalen;++i){
        j = (j + ksa[i] + k[strlen(k) - (i % strlen(k)) - 1]) % ksalen;
        tmp = ksa[i];
        ksa[i] = ksa[j];
        ksa[j] = tmp;
    }
}

void functiontwo(unsigned char *ksa, unsigned char* pt, int ksalen, int clen){
    int i=0, j=0, tmp;
    for (int s=0;s<clen;s++){
        i = (i+1) % ksalen;
        j = (j + ksa[i]) % ksalen;
        tmp = ksa[i];
        ksa[i] = ksa[j];
        ksa[j] = tmp;
        pt[s] ^= ksa[(ksa[i] + ksa[j]) % ksalen];
    }
}

unsigned char* functionfour = "1b65380f084b59016875513c6373131d2a6a327172753a2918243d7b181a051e5f1e104c32331c0842777b375f100113";

int main(int argc, char *argv[])
{
    char *pt = argv[1];
    unsigned char *k = "rc3cipherbestcipher";
    char ct[strlen(pt)*2+1];
    int ksalen = (int)functionthree(pt) + 1;
    int clen = strlen(pt);
    unsigned char ksa[ksalen];
    for (int i=0; i<clen; i++){
        pt[i] ^= k[i % strlen(k)];
    }
    functionone(ksa, k, ksalen);
    functiontwo(ksa, pt, ksalen, clen);
    ct[0] = 0;
    for (int i=0;i<clen;i++)
        sprintf(ct, "%s%02x", ct, pt[i]);
    printf("Your ciphertext is: %s\n", ct);

    if (!strcmp(functionfour, ct))
        puts("Generic response two.");
    else
        puts("Generic response one.");
    return 0;
}
