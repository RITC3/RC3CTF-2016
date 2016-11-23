#include <stdio.h>
#include <stdlib.h>
#include <string.h>

#define NUM_RECORDS 5
#ifdef ALPHA
#define ALPHA_BUILD 1
#endif
#ifdef BETA
#define ALPHA_BUILD 0
#endif

typedef struct record_t {
    char prod_code[8];
    unsigned int id;
} record_t;

int index = 0;

void print_menu(){
    puts("================================================");
    printf("|RC3 Inventory Management System (public %s)|\n", ALPHA_BUILD ? "alpha" : "beta ");
    puts("================================================");
    puts("1. Add record");
    puts("2. Delete record");
    puts("3. View record");
    puts("4. Quit");
    printf("Choose: ");
}

int process_choice(record_t records[], int *index){
    char buf[12];
    int choice, i;
    char *pos;
    fgets(buf, 12, stdin);
    choice = strtol(buf, NULL, 10);
    switch (choice){
        case 1:
            printf("Enter product ID: ");
            fgets(buf, 12, stdin);
            records[*index].id = strtoul(buf, NULL, 10);
            printf("Enter product code: ");
            fgets(buf, 12, stdin);
            if ((pos=strchr(buf, '\n')) != NULL)
                *pos = '\0';
            strncpy(records[*index].prod_code, buf, sizeof(records[*index].prod_code));
            (*index)++;
            break;
        case 2:
            printf("Enter index to delete: ");
            fgets(buf, 12, stdin);
            i = strtoul(buf, NULL, 10);
            if (i<0 || i>=*index){
                puts("That record does not exist");
                break;
            }
            for (int j=i+1; j<*index; j++){
                memcpy(&records[j-1], &records[j], sizeof(record_t));
            }
            (*index)--;
            break;
        case 3:
            printf("Enter the index of the product you wish to view: ");
            fgets(buf, 12, stdin);
            i = strtol(buf, NULL, 10);
            printf("Product ID: %d, Product Code: ", records[i].id);
            fwrite(records[i].prod_code, 8, 1, stdout);
            fflush(stdout);
            break;
        case 4:
            return 1;
    }
    return 0;
}

int main(int argc, char *argv[])
{   
    setbuf(stdout, NULL);
    char buf[8];
    record_t records[NUM_RECORDS];
    memset(&records, 0, sizeof(record_t)*NUM_RECORDS);
    while (1){
        print_menu();
        if (process_choice(records, &index))
            break;
        printf("There are %d records in the IMS\n\n", index);
    }
    return 0;
}
