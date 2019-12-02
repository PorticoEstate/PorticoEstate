You need the PHP_CodeSniffer from PEAR

link the standard you want from the Standards catalog to the corresponding catalog beneath the PEAR::PHP_CodeSniffer.

example:
ln -s /path_to_phpGroupWareapi/test/CodeSniffer/Standards/PorticoEstate /usr/share/php/PHP/CodeSniffer/src/Standards/PorticoEstate

Usage (example):

phpcs /path/to/code/my_dir --standard=PorticoEstate --report=summary --tab-width=4 --config-set show_progress 1
