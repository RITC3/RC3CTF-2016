from flask import Flask, render_template, request, send_from_directory
import os
import commands

app = Flask(__name__)

#Select your bork
@app.route('/', methods=['GET'])
def index():
    return render_template("index.html")

@app.route('/favicon.ico', methods=['GET'])
def favicon():
    return send_from_directory(os.path.join(app.root_path, 'static'),
            'favicon.ico')

@app.route('/bork', methods=['POST'])
def bork():
    filename = request.form["bork"]
    print '"' + filename + '"'
    
    #This is the difficulty.  We stop you from using some basic characters.  You need to use && to ls and cat flag.txt
    badchars = [';', '>', '<', '|', '/', '..', "python", '`', '$', '(', ')', "bash", "cp", "mv", "ruby", "perl"]
    #print "before the loop"
    for bad in badchars:
        #print bad
        if bad in filename:
            print "found bad character"
            return render_template("sad.html")

    #This is the vulnerable code.  Pro Tip: Don't concatenate strings.  It's bad news bears.
    bork = commands.getstatusoutput('cat borks/' + filename)
    #print bork
    
    return render_template("bork.html", bork=bork[1])

if __name__ == "__main__":
    app.run()
