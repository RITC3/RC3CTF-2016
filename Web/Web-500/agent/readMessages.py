#!/usr/bin/env python
import logging
from pyvirtualdisplay import Display
from selenium import webdriver
from selenium.webdriver.common.desired_capabilities import DesiredCapabilities
#from selenium.webdriver.support import expected_conditions as EC
#from selenium.webdriver.support.ui import WebDriverWait
#from selenium.webdriver.common.by import By
from selenium.webdriver.common.keys import Keys
#from sys import stderr
#from time import sleep
from sys import exit
from sys import argv

def get_privkey(name="test_key.asc"):
	contents = ""
	with open(name, "rb") as f:
		contents = f.read()

	return contents

def main():
	if len(argv) > 4:
		#if debug flag given, set debug to true and get rid of arg
		#not tested
		if argv[1] == "-d" or argv[1] == "--debug":
			logging.basicConfig(filename="selenium.log", level=logging.INFO)
			del argv[1]
		elif argv[1] == "-dd" or argv[1] == "--debug=2":
			logging.basicConfig(filename="selenium.log", level=logging.DEBUG)
			del argv[1]
		else:
			logging.basicConfig(filename="selenium.log", level=logging.WARNING)

		protocol = "http://"
		serverip = "54.172.225.153" #need this instead of localhost for referer validation and whatnot
		base = protocol + serverip
		login = base + "/login.php"
		messages = base + "/messages.php?id="
		user = argv[1]
		password = argv[2]
		privkey = get_privkey("julian_assange_priv.asc")
		passphrase = argv[3]
		pin = argv[4]

		#loop through all unread messages. attempt to read/decrypt the message
		for arg in argv[5:]:
			#login then read the current message id
			try:
				display = False
				mydriver = False

				#so we can do headless browser simulation. would use phantomjs but it blows
				display = Display(visible=0, size=(800, 600))
				display.start()

				d = DesiredCapabilities.CHROME
				d['loggingPrefs'] = { 'browser': 'ALL' }
				mydriver = webdriver.Chrome(desired_capabilities=d)
				mydriver.implicitly_wait(15) #supposed to wait 15 seconds for something. not sure if it actually does anything

				#apparently there's a bug that makes this not work:
				#	http://sqa.stackexchange.com/q/9007
				#	http://stackoverflow.com/q/40273832/1200388
				#mydriver.set_page_load_timeout(15) #set page load timeout like this too, just to be safe

				#we have to log in bc we can't set HTTPOnly cookies with selenium.
				mydriver.get(login)
				user_field = mydriver.find_element_by_id("username-field")
				mydriver.execute_script("arguments[0].value = arguments[1];", user_field, user)
				pass_field = mydriver.find_element_by_id("password-field")
				mydriver.execute_script("arguments[0].value = arguments[1];", pass_field, password)
				mydriver.find_element_by_id("login-button").click()

				#now go to page for current message
				mymessage = messages + arg
				mydriver.get(mymessage)

				#set value of pin and click button to submit form
				#have to do this instead of just posting the data ourself b/c:
				#1. can't do post requests with selenium alone. selenium-requests can do this but it's kind of a janky library
				#2. can't set referer header with selenium alone, need to use browsermob or some proxy to do that
				pin_element = mydriver.find_element_by_id("pin")
				mydriver.execute_script("arguments[0].value = arguments[1];", pin_element, pin)
				mydriver.find_element_by_id("pin-button").click()

				logging.info("page after submitting pin:")
				logging.info(mydriver.page_source)

				#we should be on the page where we can decrypt the message now. enter privkey and passphrase, click button
				privkey_element = mydriver.find_element_by_id('privkey')
				mydriver.execute_script("arguments[0].value = arguments[1];", privkey_element, privkey)
				passphrase_element = mydriver.find_element_by_id("msg-subject")
				mydriver.execute_script("arguments[0].value = arguments[1];", passphrase_element, passphrase)
				mydriver.find_element_by_id("decrypt-button").click()

				#sleep(5)

				#print out console.log
				for entry in mydriver.get_log('browser'):
					logging.warning(entry)

				logging.info("PAGE: ")
				logging.info(mydriver.page_source)
			except Exception as e:
				try:
					logging.warning("in except for id=" + str(arg))
					logging.error(e)
				except:
					pass
			else:
				#if no errors, print message id (arg)
				if mydriver:
					logging.warning("successfully read id=" + str(arg))
					print arg
			finally:
				if display:
					display.stop()

				#make sure mydriver exists and stuff before closing it or printing out
				if mydriver:
					mydriver.quit()

		return 0
	else:
		logging.error("not enough args given")
		return -1

if __name__ == "__main__":
	exit(main())

