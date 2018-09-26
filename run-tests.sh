#!/bin/bash

# Log 
function log_output() {
	echo $(date) :: Territory Services Api - Tests :: $1
}

# Refresh database if option passed (--refresh)
function refresh_db() {
	rm database/database.sqlite
	touch database/database.sqlite
	php artisan migrate:refresh --seed --database=sqlite
	log_output "DB refreshed"
}

# BASH_ARGV
flags=""
for a in ${BASH_ARGV[*]} ; do
	# echo Flag: "$a "
	if [[ $a == *"--refresh"* ]]
		then 
			log_output "Need to refresh DB"; 
			refresh_db
	fi
	if [[ $a == *"--nostyle"* ]]
		then 
			flags="$flags --colors=never"
			# log_output "Flag is set --colors=never";
	fi
done

# echo Final Flags: ${flags}


./vendor/bin/phpunit ${flags}