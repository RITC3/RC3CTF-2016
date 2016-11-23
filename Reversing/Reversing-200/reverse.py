from sys import argv,stdout
with open(argv[1]) as f:
    stdout.write(f.read()[::-1])
