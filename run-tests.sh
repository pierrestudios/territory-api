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

log_output "Refresh DB"; 
refresh_db

flags=" --log-json $report_file"
for a in ${BASH_ARGV[*]} ; do
	# echo Flag: "$a "
	if [[ $a == *"--nostyle"* ]]
		then 
			flags="$flags --colors=never"
			# log_output "Flag is set --colors=never";
	fi
done

./vendor/bin/phpunit ${flags}