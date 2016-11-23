GoReverseMe
===========
GoLang reversing challenge.  

step1 derives the password by xoring a byte array with 0x69 to get 'golang-or-bust'

step2 makes an encrypted zip with the password derived above

step3 is bogus and always returns 0

step4 encrypts the zip by starting from the first byte in the file and subtracting the next byte from it (anding with 0xff to keep it a byte). It does this until the end leaving the last byte.

Do this in reverse to get the flag :)
