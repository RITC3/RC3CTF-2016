#include <stdio.h>
#include <stdlib.h>
#include <sys/types.h>
#include <sys/socket.h>
#include <unistd.h>
#include <string.h>
#include <netinet/in.h>
#include <arpa/inet.h>

#define perr(prefix) if (PERROR){perror(prefix);}
#ifdef DEBUG
#define PERROR 1
#else
#define PERROR 0
#endif

#define zmem(a) memset(&a, 0, sizeof(a))

/* vuln ideas:
 * border is format string vuln to leak an addr
 * don't let people send to themselves, unless they overflow something
 * some kind of use after free?
 * */
typedef struct Card_s {
    char recipient_name[50];
    char sender_name[50];
    char recipient_ip[16];
    unsigned short recipient_port;
    char border[4];
    char *contents;
} Card;

char *ending = "   -- Made with Cardmaker";

#ifdef DEBUG
Card *populate_card(char *border, char* rname, char *sname, char *recip, short rport, char *contents){
    Card *card = (Card*)malloc(sizeof(Card));
    strncpy(card->border, border, 2);
    strncpy(card->recipient_name, rname, 50);
    strncpy(card->sender_name, sname, 50);
    strncpy(card->recipient_ip, recip, 15);
    card->recipient_port = rport;
    card->contents = contents;
    return card;
}
#endif

char *print_card(Card* card){
    /* chars per line 50
     * split on space, keep track of chars
     * if msg is > 50 chars width is 50 + 12 padding,
     * otherwise it is strlen(contents)+12+newline
     * total len = width*height
     */
    strcat(card->contents, ending);
    int i,j;
    int content_height = strlen(card->contents)/50;
    int content_width = (content_height ? 50 : strlen(card->contents));
    int width =  content_width + 10;
    int height = content_height + 1 + 12;
    int bsz = width+strlen(card->border)*2+1;
    char *linebuf = (char*)malloc(bsz);
    //char *buf = (char*)malloc(width*height);
    //print first line
    memset(linebuf, 0, bsz);
    for (i=0;i<width/strlen(card->border)+2;i++)
        strcat(linebuf, card->border);
    printf(linebuf);
    printf("\n");
    //print border lines until (height-content_height)/2
    for (i=0;i<(height-content_height)/2;i++){
        memset(linebuf, 0, bsz);
        strcat(linebuf, card->border);
        for (j=0;j<width;j++)
            strcat(linebuf, " ");
        strcat(linebuf, card->border);
        printf(linebuf);
        printf("\n");
    }
    //print card lines
    for (i=0;i<content_height+1;i++){
        memset(linebuf, 0, bsz);
        int tcwidth = content_height == i ? strlen((card->contents)+(i*content_width)) : content_width;
        strcat(linebuf, card->border);
        for (j=0;j<(width-tcwidth)/2;j++){
            strcat(linebuf, " ");
        }
        strncat(linebuf, (card->contents)+(i*content_width), tcwidth);
        for (j=0;j<(width-tcwidth)/2;j++){
            strcat(linebuf, " ");
        }
        strcat(linebuf, card->border);
        strcat(linebuf, "\n");
        printf("%s", linebuf);
    }
    //print border lines until (height-content_height)/2
    for (i=0;i<(height-content_height)/2;i++){
        memset(linebuf, 0, bsz);
        strcat(linebuf, card->border);
        for (j=0;j<width;j++)
            strcat(linebuf, " ");
        strcat(linebuf, card->border);
        printf(linebuf);
        printf("\n");
    }
    //print last line
    memset(linebuf, 0, bsz);
    for (i=0;i<width/strlen(card->border)+2;i++)
        strcat(linebuf, card->border);
    printf(linebuf);
    printf("\n");
    free(linebuf);
}

int send_card(Card* card){
    struct sockaddr_in rsin;
    zmem(rsin);
    int lsock = socket(AF_INET, SOCK_STREAM, 0);
    if (lsock < 0) {
        perr("sock");
        return -1;
    }

    rsin.sin_family = AF_INET;
    rsin.sin_port = htons(card->recipient_port);
    rsin.sin_addr.s_addr = inet_addr(card->recipient_ip);

    if (connect(lsock, (struct sockaddr*)&rsin, sizeof(rsin)) < 0){
        //perr("connect");
        return -1;
    }
    int saved = dup(1);
    dup2(lsock, 1);
    printf("Hi %s, %s sent you a card!!!\n", card->recipient_name, card->sender_name);
    print_card(card);
    dup2(saved, 1);
    close(saved);
    close(lsock);
    printf("Dad?");

    return 0;
}

void print_menu(){
    puts("Welcome to the greeting card maker!");
    puts("1. New greeting card");
    puts("2. List greeting cards to be sent");
    puts("3. Change the contents of a card");
    puts("4. Delete a greeting card in the queue");
    puts("5. Send all greeting cards in the queue");
    puts("6. Quit");
}

int main(int argc, char *argv[])
{
    Card cardq[5];
    char inputbuf[50];
    int choice, msize, nread, ncards = 0;
    size_t tmp;
    char *chptr;
    setbuf(stdout, NULL);
    while (1){
        print_menu();
        printf("Choice: ");
        fgets(inputbuf, 15, stdin);
        choice = strtol(inputbuf, NULL, 10);
        switch (choice){
            case 1: 
                if (ncards == 5){
                    puts("The card queue can only hold 5 cards :( send one or delete one and try again");
                    break;
                }
                puts("Who is this card from?");
                fgets(cardq[ncards].sender_name, 50, stdin);
                chptr = strchr(cardq[ncards].sender_name, '\n');
                if (chptr)
                    *chptr = 0;
                puts("Who is this card going to?");
                fgets(cardq[ncards].recipient_name, 50, stdin);
                chptr = strchr(cardq[ncards].border, '\n');
                if (chptr)
                    *chptr = 0;
                puts("What address and port should I send this to? (ip:port)");
                fgets(inputbuf, 50, stdin);
                chptr = strtok(inputbuf, ":");
                if (!chptr){
                    puts("IP or port not valid.");
                    break;
                }
                strncpy(cardq[ncards].recipient_ip, chptr, 14);
                chptr = strtok(NULL, ":");
                if (!chptr){
                    puts("IP or port not valid.");
                    break;
                }

                cardq[ncards].recipient_port = strtol(chptr, NULL, 10);
                if (cardq[ncards].recipient_port > 65535){
                    puts("IP or port not valid.");
                    break;
                }
                puts("What border would you like around the card? (max of 2 chars)");
                fgets(cardq[ncards].border, 4, stdin);
                chptr = strchr(cardq[ncards].border, '\n');
                if (chptr)
                    *chptr = 0;
                cardq[ncards].border[2] = 0;
                //have you tried 4294967295 ?????
                puts("How long is your message...?");
                fgets(inputbuf, 15, stdin);
                if (inputbuf[0] == '-'){
                    puts("Card size can't be negative. Returning to menu.");
                    break;
                }
                msize = strtol(inputbuf, NULL, 10);
                // BECAUSE THIS IS A GOOD THING TO DO, yee olde integer overflow bug
                // USE THE FORCE, LUKE
                cardq[ncards].contents = malloc((unsigned long)msize + strlen(ending));
                puts("What would you like your card to say? (end with 'done.' on its own line)");
                nread = 0;
                while (nread < (unsigned int)msize){
                    tmp = msize-nread;
                    chptr = (cardq[ncards].contents)+nread;
                    nread += getline(&chptr, &tmp, stdin);
                    if (!strncmp(chptr, "done.\n", 2)){
                        chptr--;
                        for (int i = 0; i<strlen("done.\n"); ++i)
                            chptr[i] = 0;
                        break;
                    }
                }
                chptr = cardq[ncards].contents;
                while (chptr){
                    chptr = strchr(chptr+1, '\n');
                    if (chptr)
                        *chptr = ' ';
                }
                ncards++;
                break;
            case 2: 
                printf("Which card do you want to print the fields of: ");
                fgets(inputbuf, 5, stdin);
                choice = strtol(inputbuf, NULL, 10);
                if (choice < 1 || choice > ncards){
                    puts("Invalid card");
                    break;
                }
                choice--;
                printf("From: %s\nTo: %s\nIP: %s\nPort: %d\nBorder: %s\nContents: %s\n", cardq[choice].sender_name, cardq[choice].recipient_name, cardq[choice].recipient_ip, cardq[choice].recipient_port, cardq[choice].border, cardq[choice].contents);
                break;
            case 3:
                printf("Which card do you want to change the contents of: ");
                fgets(inputbuf, 5, stdin);
                choice = strtol(inputbuf, NULL, 10);
                if (choice < 1 || choice > ncards){
                    puts("Invalid card");
                    break;
                }
                choice--;
                msize = strlen(cardq[choice].contents) + 3;
                fgets(cardq[choice].contents, msize, stdin);
                break;
            case 4:
                printf("Which card do you want to delete: ");
                fgets(inputbuf, 5, stdin);
                choice = strtol(inputbuf, NULL, 10);
                if (choice < 1 || choice > ncards){
                    puts("Invalid card");
                    break;
                }
                free(cardq[choice].contents);
                for (int i = choice; i < ncards; i++)
                    memcpy(&cardq[i-1], &cardq[i], sizeof(Card));
                ncards--;
                break;
            case 5:
                for (int i = 0; i < ncards; ++i)
                    send_card(&(cardq[i]));
                break;
        }
        if (choice == 6)
            break;
    }
    for (int i=0; i<ncards; ++i)
        free(cardq[i].contents);
    return 0;
}
