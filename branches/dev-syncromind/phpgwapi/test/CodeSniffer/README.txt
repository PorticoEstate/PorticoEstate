You need the PHP_CodeSniffer from PEAR

link the standard you want from the Standards catalog to the corresponding catalog beneath the PEAR::PHP_CodeSniffer.

example:
ln -s /path_to_phpGroupWareapi/test/CodeSniffer/Standards/phpGroupWare /usr/local/lib/php/PHP/CodeSniffer/Standards/phpGroupWare

Usage (example):

phpcs /path/to/code/my_dir --standard=phpGroupWare --report=summary --tab-width=4
