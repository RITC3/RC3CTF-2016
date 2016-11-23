FLE
===
If you solve the dummy challenge (which is really easy, just an xor with "dad?") then you get RC3-NOT-THE-FLAG-YOURE-LOOKING-FOR  
How unfortunate.  

The trick is in the name!  
It's ELF backwards.  
So if you reverse the file byte-by-byte, and fix the header (FLAG -> \x7fELF) you can run the binary again but backwards.

The backwards ELF references data from the forward ELF. XOR the values at the offsets 9 with the reverse of the encrypted string from the first challenge to get the flag!
