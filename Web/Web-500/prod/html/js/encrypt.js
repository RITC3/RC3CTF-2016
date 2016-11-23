function encryptAndSend() {
	var pubkey = document.getElementById("dest-pubkey").textContent.trim();
	var origMessage = document.getElementById("origMessage");
	var encMessage = document.getElementById("encMessage");
	//var encP = document.getElementById("encP");
	//console.log("original message: " + origMessage.value);
	var user;
	kbpgp.KeyManager.import_from_armored_pgp({
	  armored: pubkey
	}, function(err, user) {
		if (!err) {
			var params = {
				msg:         origMessage.value,
				encrypt_for: user
			};

			//encrypt message, set input value and submit form
			kbpgp.box(params, function(err, result_string, result_buffer) {
				if(!err) {
					encMessage.value = result_string;
					/*
					encP.innerHTML = result_string;
					var test1 = encMessage.value;
					var test2 = encP.textContent.trim();
					if(test1 === test2)
						console.log("they're equal!");
					else if (test1 == test2)
						console.log("they're truthy");
					*/
					origMessage.value = "";
					document.getElementById("message-form").submit();
				} else {
					alert("Error encrypting message, try again later.");
				}
			});
	    }else {
			alert("Error using public key, try again later.");
		}
	});
}
