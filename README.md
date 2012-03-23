# A PHP port of the YUI CSS compressor

This port is based on version 2.4.7 (Sep 26, 2011) of the [YUI compressor](https://github.com/yui/yuicompressor).

**Table of Contents**

1.  [How to use](#howtouse)
2.  [YUI compressor on asteroids!](#onasteroids)
    1.  [Bugs fixed](#bugsfixed)
    2.  [Enhancements](#enhancements)
3.  [Unit Tests](#unittests)
4.  [API Reference](#api)
5.  [Who uses this?](#whousesit)
6.  [TODOs](#todos)

<a name="howtouse"></a>

## 1\. How to use

```php
<?php

// Require the compressor
require 'cssmin.php';

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

<a name="onasteroids"></a>

## 2\. YUI compressor on asteroids!

<a name="bugsfixed"></a>

### 2.1\. FIXED BUGS still present in the original YUI compressor

* `border-left: none;` gets compressed to `border-left:0`. YUI compressor has a typo in a regular expression. See issue [here](https://github.com/yui/yuicompressor/pull/23).
* Only one `@charset` at-rule per file and pushed at the beginning of the file. YUI compressor does not remove all @charset at-rules.
* Safer/improved comment removal. YUI compressor would ruin part of the output if the `*` selector is used right after a comment: `a{/* comment 1 */*width:auto;}/* comment 2 */* html .b{height:100px}`. See issues [#2528130](http://yuilibrary.com/projects/yuicompressor/ticket/2528130), [#2528118](http://yuilibrary.com/projects/yuicompressor/ticket/2528118) & [this topic](http://yuilibrary.com/forum/viewtopic.php?f=94&t=9606)
* `background: none;` is not compressed to `background:0;` anymore. See issue [#2528127](http://yuilibrary.com/projects/yuicompressor/ticket/2528127).
* `text-shadow: 0 0 0;` is not compressed to `text-shadow:0;` anymore. See issue [#2528142](http://yuilibrary.com/projects/yuicompressor/ticket/2528142)
* Trailing `;` is not removed anymore if the last property is prefixed with a `*` (lte IE7 hack). See issue [#2528146](http://yuilibrary.com/projects/yuicompressor/ticket/2528146)
* Fix for issue [#2528093](http://yuilibrary.com/projects/yuicompressor/ticket/2528093).

<a name="enhancements"></a>

### 2.2\. ENHANCEMENTS over the original YUI compressor

* Signed numbers (+-) are compressed correctly. So `+0.2em` gets minified to `.2em`, `-0.4%` to `-.4%` and `+0.000em` to `0`. See request [here](http://yuilibrary.com/forum/viewtopic.php?f=94&t=9307).
* Added newer unit lengths `ch, rem, vw, vh, vm, vmin` so we can replace `0rem` or `0vw` with `0`.
* More `0` unit length values are replaced to `0` i.e. `.0em`, `0.0000in`, `-0px` ... every possible 0 length value, not just `0(px,in,em...)`!
* Percentage and negative RGB values in the functional notation are supported i.e. `rgb(100%, 0%, 0%)` gets minified to `#f00`.
* RGB colors outside the sRGB color space (`0 - 255` or `0% - 100%`) are clipped i.e. `rgb(280, -1, -100)` gets minified to `#f00` because it's the same as `rgb(255, 0, 0)`.
* HSL colors are compressed to hexadecimal, i.e. `hsl(0, 100%, 50%)` gets minified too to `#f00`. HSL angles are wrapped and values are clipped if needed.
* All regular expressions are case insensitive.

<a name="unittests"></a>

## 3\. Unit Tests

Unit tests are updated according to all bug fixes and enhancements made so do not run YUI's original unit tests against this port.

More than 50 unit tests and many more to come!!

How to run the test suite:

* You need a server with PHP 4+ installed.
* Download the code and upload it to a folder in your server.
* Open your favourite browser and enter the URL to the file `tests/run.php`.

<a name="api"></a>

## 4\. API Reference

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

<a name="whousesit"></a>

## 5\. Who uses this port?

* [Minify](https://github.com/mrclay/minify)

<a name="todos"></a>

## 6\. TODOs

* Style the unit tests output!
* Add more unit tests (from Microsoft's AjaxMin)
* Some shorthand optimizations
* Even better colors compression