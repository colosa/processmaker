all: build test

build:
	#plex lib/Haanga/Compiler/Lexer.lex
	phplemon lib/Haanga/Compiler/Parser.y


test: 
	cd tests; ~/bin/php-5.2/bin/php /usr/bin/phpunit --colors --verbose TestSuite.php
	cd tests; php /usr/bin/phpunit --coverage-html coverage/ --colors --verbose  TestSuite.php

test-fast:
	cd tests; php /usr/bin/phpunit --stop-on-failure --colors --verbose  TestSuite.php


edit:
	vim lib/Haanga/Compiler/Parser.y  lib/Haanga/Compiler/Tokenizer.php -O
