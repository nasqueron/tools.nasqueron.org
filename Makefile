#
# Nasqueron Tools dependencies Makefile
#
# Fetches dependencies through Composer and moves them to the right locations.
#

all:
	composer install
	mv vendor/dereckson/source-templates-generator wikimedia/write/sourcetemplatesgenerator

clean:
	rm -rf vendor composer.lock wikimedia/write/sourcetemplatesgenerator
