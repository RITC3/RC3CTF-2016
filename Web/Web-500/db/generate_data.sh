#!/usr/bin/env bash

passSalt='what a great salt amirite?'
pinSalt='w0wzer$ even better salttttttt'
hash='sha512sum'

admin='admin D5z5mjziO$TC0ec#Q&hhj6YCv X1wbresi'
wikileaks='wikileaks PK^*ax^Gt5e3sNNtwFA#dUZb5 nKE3jX1iW3'
julian='julianassange St#I4OFn8u@8PH!AIeRJg*h44 03071971'
darkarmy='darkarmy TUPeT*crS2SorpZ4lRfYS*^OV R1lhRDHP0B'
users="admin wikileaks julian darkarmy"

#print data for each user
for user in $users; do
	eval u=\$$user
	declare -A data
	i=0
	for val in $u; do
		data[$i]="$val"
		((i++))
	done

	echo "data for ${data[0]}"
	echo -n "${passSalt}${data[0]}${data[1]}" | $hash | awk '{ print $1 }'
	echo -n "${pinSalt}${data[0]}${data[2]}" | $hash | awk '{ print $1 }'
	echo
done

