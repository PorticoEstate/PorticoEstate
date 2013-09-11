<?php
/**
 * Use this class to transform a html/a url to a pdf
 *
 * @package Snappy
 * @author Matthieu Bontemps<matthieu.bontemps@knplabs.com>
 * https://github.com/knplabs/snappy
 * The MIT License
 *
 * Copyright (c) 2010 Matthieu Bontemps
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

class SnappyPdf extends SnappyMedia
{
    protected $defaultExtension = 'pdf';
    protected $options = array(
        'ignore-load-errors' => null,                          // old v0.9
        'lowquality' => true,
        'username' => null,
        'password' => null,
        'minimum-font-size'	=> 16,
    );
    
}
?>
