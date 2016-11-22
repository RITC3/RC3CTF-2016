#Miscellaneous 300 - Klaatu Barada N...

##FLAG: RC3-2016-CHRLSD3D
This flag is pronounced "Cheryl's dead", which is a reference to a character in the Evil Dead series.

##Description
Whilst fighting of hordes of Deadites, Ash seems to have forgotten something.  Help Ash remember the words.

##Overview
The Competitor will be tasked with connecting to this server.  When the server receives a connection, it dumps back a ton of base64 encoded strings.  These strings contain quotes from the Evil Dead series and only exist to be awesome.  The real information is carried in the encoded strings.  If there is a single "=" present at the end of the string, then the quote represents a 1.  If there are two equals signs present at the end of the string, then the quote represents a 0.  Stringing the resultant bit together will result in the binary version of the ASCII formatted flag.  Submit the flag for points.

##Hints
The hints for this challenge are as follows:
    1. There seems to be a pattern if I'm not mistaken
    2. If you're decoding the base64, you're gonna have a bad time
