BFLAGS=-m32 -std=c99
EFLAGS=-z execstack -fno-stack-protector -static

all: IMS.c easy/IMS-easy hard/IMS-hard

debug: BFLAGS += -ggdb
debug: all

easy/IMS-easy:
	gcc $(BFLAGS) $(EFLAGS) -DALPHA -o easy/IMS-easy IMS.c
hard/IMS-hard:
	gcc $(BFLAGS) -DBETA -o hard/IMS-hard -z relro -z now IMS.c
clean:
	rm -rf easy/IMS-easy
	rm -rf hard/IMS-hard
