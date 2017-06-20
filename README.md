# Expect

This package is a pure PHP alternative to [expect](https://en.wikipedia.org/wiki/Expect), the Unix tool.  This package doesn't depend on the [PECL package](https://pecl.php.net/package/expect) either.

Expect lets you script interactions with interactive terminal applications.

## Why?

The original author wrote this "because I wrote an interactive CLI program and needed to write automated tests for it.  Apparently people use the real expect for scripting ftp and telnet workflows, so I guess you could use it for that too."

I have modified this for my own uses.

## Installation

For now, copy the Expect.php file to your own project and use include_once with the path of the file to add it

## API

This version does not support chaining of commands. It follows the program flow as defined within your php application.

## Examples

There is an example in the source file Expect.php
