CFLAGS=-std=gnu99 -Wno-format-security

all: debug
debug: CFLAGS+=-ggdb -DDEBUG
debug: cardmaker

cardmaker: cardmaker.c
	gcc -o cardmaker $(CFLAGS) cardmaker.c

clean:
	rm -f cardmaker
