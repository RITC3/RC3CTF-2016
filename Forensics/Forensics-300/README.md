#Forensics 300 - Breaking News
For this challenge, everything in the ```prod``` directory will be distributed to competitors.  Everything in the ```dev/``` directory is for development only.

##Description
We just received this transmission from our news correspondents.  We need to find out what they are telling us.

##Flag: RC3-2016-DUKYFBLS
The flag is pronounced "Ducky Fabulous"

##Overview
The Competitior is given a tarball that is compressed.  When they open it, they see files named Chapter0.zip through Chapter19.zip.  Each of these files are zip files, but 5 of them have data appended to them.  This data is Base64 encoded and then appended to the file.  The files are in numerical order, not alphabetical, by the chapters.  It should be noted that there are 22 bytes of data after the final offset displayed by binwalk in every valid zip file.  The files that have the data for the flag in them will have 22 + n bytes of data, where the 22 bytes is the standard space present after the final offset noted in binwalk, and 'n' is the number of bytes that contain the data for the flag.

Extracting each of the files reads a line from a story that I made up that means basically nothing important, and is just nonsensical chitter chatter to throw you off.  The flag is pronounced "Ducky Fabulous", which is the only reference to the story, as one of the characters is a duck.  Teehee :P

##Hints
The hints for this challenge are as follows:
    1. It looks like there's more to this than meets the eye. 
    2. Maybe the conspiracy does run deeper than he thought.  Or maybe more shallow...
    3. All is not as it seems with this case.  If we read between the lines, we may be able to find out what is happening.


##Flag Composition Summary
Flag: RC3-2016-DUKYFBLS       //Will match the result of decoding each covert string
Base64: UkMzLTIwMTYtRFVLWUZCTFMK  //Will not match the base64 encoded blobs below
Chunks
    Chunk 1
        ASCII: RC
        Base64: UkMK
        Appended to: Chapter4.zip
    Chunk 2
        ASCII: 3-20
        Base64: My0yMAo=
        Appended to: Chapter9.zip
    Chunk 3
        ASCII: 16-DU
        Base64: MTYtRFUK
        Appended to: Chapter10.zip
    Chunk 4
        ASCII: KYF
        Base64: S1lGCg==
        Appended to: Chapter15.zip
    Chunk 5
        ASCII: BLS
        Base64: QkxTCg==
        Appended to: Chapter18.txt
