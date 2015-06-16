#
# Nasqueron Tools dependencies Makefile
#
# Fetches dependencies through Composer and moves them to the right locations.
# Compiles JSX files.
#

BROWSERIFY=node_modules/.bin/browserify

all: vendor wikimedia/write/sourcetemplatesgenerator node_modules compilejs

vendor:
	composer install

wikimedia/write/sourcetemplatesgenerator:
	mv vendor/dereckson/source-templates-generator wikimedia/write/sourcetemplatesgenerator

node_modules:
	npm install
	npm dedupe

compilejs: gadgets/generators/bundle.js

gadgets/generators/bundle.js:
	browserify --transform reactify gadgets/generators/app.js > gadgets/generators/bundle.js

cleanjs:
	rm -f gadgets/generators/bundle.js

clean: cleanjs
	rm -rf vendor composer.lock wikimedia/write/sourcetemplatesgenerator
