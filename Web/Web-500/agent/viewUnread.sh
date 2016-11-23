#!/usr/bin/env bash

export PYTHONIOENCODING=UTF-8

sleeptime="30"
selenium_log="selenium.log"
user="julianassange"
password='St#I4OFn8u@8PH!AIeRJg*h44'
passphrase='4ll_H41L_w1k1L34K$-03071971'
pin="03071971"
readMessages="readMessages.py"
unreadSQL="unread.sql"
sqluser="dba"
sqlpass='YupThisIsDaNewDBP4$$'

while true; do
	messages=$(mysql < $unreadSQL -u $sqluser -p$sqlpass 2>/dev/null)
	if [ "$messages" = "" ]; then
		echo "no unread messages, sleeping $sleeptime..."
		sleep $sleeptime
		continue
	fi
	set $messages
	shift

	echo we got $@ as unread message ids

	#need to write stderr when calling this script instead
	#results=$(python -u $readMessages $user $password $passphrase $pin $@)
	python $readMessages $user $password $passphrase $pin $@
	#results=$(python $readMessages $user $password $passphrase $pin $@ 2>> $selenium_log)

	#clean up processes just in case something terrible happened
	pkill chromedriver
	pkill chrome
	pkill Xvfb
	#echo -n "unread message ids: "
	#for msg in $messages; do
	#	if [ "$msg" != "id" ]; then
	#		echo -n "$msg "
	#	fi
	#done

	#echo -n "successful read of: "
	#for msg in $results; do
	#	echo -n "$msg "
	#done
	#echo
	sleep $sleeptime
done

