all: fle.out

fle.out: bytes fle.asm
	nasm -f bin -o fle.out fle.asm
	chmod +x fle.out

dummy: dummy.c
	gcc -o dummy -std=c99 dummy.c

bytes: dummy
	python makebytes.py dummy

final: fle.out
	python reverse.py fle.out > fle
	chmod +x fle

clean:
	rm -f fle fle.out bytes dummy
