# A PHP port of the YUI CSS compressor

## Who uses this port?

* [Minify](https://github.com/mrclay/minify)

## How to use

```php
<?php

// Require the compressor
include 'cssmin.php';

// Extract the CSS code you want to compress from your CSS files
$input_css1 = file_get_contents('test1.css');
$input_css2 = file_get_contents('test2.css');

// Create a new CSSmin object.
// By default CSSmin will try to raise PHP settings.
// If you don't want CSSmin to raise the PHP settings pass FALSE to
// the constructor i.e. $compressor = new CSSmin(false);
$compressor = new CSSmin();

// Override any PHP configuration options before calling run() (optional)
$compressor->set_memory_limit('256M');
$compressor->set_max_execution_time(120);

// Compress the CSS code in 1 long line and store the result in a variable
$output_css1 = $compressor->run($input_css1);

// You can change any PHP configuration option between run() calls
// and those will be applied for that run
$compressor->set_pcre_backtrack_limit(3000000);
$compressor->set_pcre_recursion_limit(150000);

// Compress the CSS code splitting lines after a specific column (2000) and
// store the result in a variable
$output_css2 = $compressor->run($input_css2, 2000);

// Do whatever you need with the compressed CSS code
echo $output_css1 . $output_css2;
```

## Some Notes

This port is based on [commit 91c5ea5 (Sep 26, 2011)](https://github.com/yui/yuicompressor/commit/91c5ea5ba37d8f969c3939e3b33a1296c561b872) of the javascript version of the YUI compressor "cssmin.js".

Bugs fixed in this port but present in YUI compressor:

* `a{border-left: none;}` is minified to `a{border-left:0}`. See issue [here](https://github.com/yui/yuicompressor/pull/23).
* Only one `@charset` at-rule per file and pushed at the beginning of the file. YUI compressor does not remove all @charset at-rules.
* Safer/improved comment removal. YUI compressor will ruin part of the output if you use the star IE hack right after a comment: `a{/* comment 1 */*width:auto;/* comment 2 */height:100px}`. See issue [here](http://yuilibrary.com/forum/viewtopic.php?f=94&t=9606)

## Tests

How to run the test suite:

* You need a server with PHP installed.
* Open your browser and navigate to the file `run_tests.php`.

## API methods

### __construct([ bool *$raise_php_limits* ])

**Description**

Class constructor, creates a new CSSmin object.

**Parameters**

*raise_php_limits*

If TRUE, CSSmin will try to raise the values of some php configuration options.
Set to FALSE to keep the values of your php configuration options.
Defaults to TRUE.

### run(string *$css* [, int *$linebreak_pos* ])

**Description**

Minifies a string of uncompressed CSS code.
`run()` may be called multiple times on a single CSSmin instance.

**Parameters**

*css*

A string of uncompressed CSS code.
Defaults to an empty string `''`.

*linebreak_pos*

Some source control tools don't like it when files containing lines longer than, say 8000 characters, are checked in.
The linebreak option is used in that case to split long lines after a specific column.
Defaults to FALSE (1 long line).

**Return Values**

A string of compressed CSS code or an empty string if no string is passed.

### set_memory_limit(mixed *$limit*)

**Description**

Sets the `memory_limit` configuration option for this script

CSSmin default value: `128M`

**Parameters**

*limit*

Values & notes: [memory_limit documentation](http://php.net/manual/en/ini.core.php#ini.memory-limit)

### set_max_execution_time(int *$seconds*)

**Description**

Sets the `max_execution_time` configuration option for this script

CSSmin default value: `60`

**Parameters**

*seconds*

Values & notes: [max_execution_time documentation](http://php.net/manual/en/info.configuration.php#ini.max-execution-time)

### set_pcre_backtrack_limit(int *$limit*)

**Description**

Sets the `pcre.backtrack_limit` configuration option for this script

CSSmin default value: `1000000`

**Parameters**

*limit*

Values & notes: [pcre.backtrack_limit documentation](http://php.net/manual/en/pcre.configuration.php#ini.pcre.backtrack-limit)

### set_pcre_recursion_limit(int *$limit*)

**Description**

Sets the `pcre.recursion_limit` configuration option for this script.

CSSmin default value: `500000`

**Parameters**

*limit*

Values & notes: [pcre.recursion_limit documentation](http://php.net/manual/en/pcre.configuration.php#ini.pcre.recursion-limit)