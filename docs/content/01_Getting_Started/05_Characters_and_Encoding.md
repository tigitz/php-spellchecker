# Characters and Encoding

A PHP string is just a sequence of bytes, with no encoding tagged to it whatsoever.

String values can come from various sources: the client (over HTTP), a database, a file, or from string literals in your source code. PHP reads all these as byte sequences, and it never extracts any encoding information.

`Text` default class is relying on the `symfony/string` project to perform string manipulation while spellchecking and **assumes an Unicode string**.

Read the library documentation to understand the problem it solves: [https://symfony.com/doc/current/components/string.html](https://symfony.com/doc/current/components/string.html)
