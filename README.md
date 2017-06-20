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


Spawn a new instance of expect for the given command. You can optionally specify a working directory and a PSR compatible logger to use.

```php
expect(string $output, $timeout = 9999999)
```

Expect the given text to show up on stdout.  Expect will block and keep checking the stdout buffer until your expectation shows up or the timeout is reached, whichever comes first.

You can use [shell wildcards](http://tldp.org/LDP/GNU-Linux-Tools-Summary/html/x11655.htm) to match parts of output.

```php
send(string $msg)
```

Send the given text on stdin.  A newline is added to each string to simulate pressing enter.  If you want to just send enter you can do `send(PHP_EOL)`

## Examples

There is an example in the source file Expect.php
