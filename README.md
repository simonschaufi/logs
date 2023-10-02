# CoStack.Logs

[![pipeline status](https://gitlab.com/co-stack.com/co-stack.com/typo3-extensions/logs/badges/master/pipeline.svg)](https://gitlab.com/co-stack.com/co-stack.com/typo3-extensions/logs/pipelines)

## Introduction

Finally, a backend module to read from the TYPO3 logging API **and** a deprecation log reader.

Notice: This extension currently supports just log tables.
If you need a special log reader (e.g. file or syslog) feel free
to contact me or create a pull request with your proposal ;)

## Basic Usage

The logging API exists for a long time, but there is no nice way to read
those logs if the user has no direct access to the database or files.

Backend module created - problem solved!

The ConjunctionReader creates an instance of `Reader` for each `Writer`,
which reads from the specific location where the writer writes the log
entries. Therefore, it's possible to read and filter logs across all log
destinations (tables, files, etc.)

## TYPO3 & PHP compatibility

The current major version 3.1 requires PHP 7.1 or higher and support TYPO3 version 9 and 10.

For versions supporting older PHP and / or TYPO3 versions please use the previous tags. Keep in mind, that these are not
actively supported, and you should strongly consider upgrading your TYPO3 installation.

## TODO

* Register as devlog
* Identify and fix that one bug that sometimes won't delete a log entry
* Add a log writer filter (to deselect log readers/erasers) #4

## Known issues

* Deleting a log entry is not executed without an error message if the used webserver is nginx and the log message
  contains a semicolon

## Found a bug? Feedback?

Please report any bug or negative side effects, as well as your feedback
here: [Issues](https://gitlab.com/co-stack.com/co-stack.com/typo3-extensions/logs/-/issues)

## Other stuff

Copyright & Author: Oliver Eglseder <oliver.eglseder@co-stack.com>
License: GPL-2.0+
