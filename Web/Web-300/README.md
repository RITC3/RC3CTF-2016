#Web 300 - Bork Bork

##Description
We all love doggos and puppers.  Have some more of one of our favorite puppers, Gabe.  Bork.

##Flag: RC3-2016-L057d0g3
This will be in the file "bork.txt"

##Overview
This is a command injection vulnerability.  The initial web page allows you to select a Gabe the dog video to watch.  I mean, who doesn't love a good Gabe the Dog video, right?  This is done via a POST to another page that contains the name of a file.  Each one of these files contains a URL to a YouTube video to play.  Since the server uses string concatenation to read in the contents of the file, you can inject bash commands into the POST parameter if you use a web proxy or an extension to modify headers and parameters.  Simple filtering is performed to prevent people from breaking the server, as well as to make this a little harder.  Characters such as ';', '|', '>', and '<', are detected by the application and will return a sad page with a sad doggo video.  The proper way to solve the challenge is to use && to execute commands.  It should be noted that there is also a "bashrc" file present in this repository to be used as the .bashrc file for the user running the server.  This contains common commands to limit the impact of ne'er do wells against the server.  

##Setup Instructions
Install the required python modules.  This is a flask web app.  Run bork.py to run the server.  It will run on port 5000.

##Hints
The hints for this challenge are as follows:
    1. Look at all those borks.  How could one bork any more?
    2. I have so many borks filed away.  Look at all of my borks.
    3. I command you to give me more borks!!!!

##CTF Debrief
During the CTF, many problems plagued Bork Bork.  The problem that impacted the stability of the challenge was that it would get stuck on certain injections.  This normally happened with command injections that dropped the user into a "subshell".  I mean this both literally as in the ```$()``` sequence in Bash, as well as scripting languages such as python.  The original list of bad phrases included this.

```
badchars = [';', '>', '<', '|', '/', '..', "python", '`']
```
This was then expanded to something along the lines of the following to improve stability.

```
badchars = [';', '>', '<', '|', '/', '..', "python", '`', '$', '(', ')', "bash", "cp", "mv", "ruby", "perl"]
```

This was not the intended behavior, and these filters did not inhibit the inteded solution of ```&&``` being used.  You may see writeups stating that the characters were filtered, and others that state that they were not.

These problems could have been rememdied by a good WSGI implementation or using a multiprocessed version of Flask so that the entire server did not hang.  These are considerations to make if you want to make a command injectable web application.
