<?php

namespace tubalmartin\CssMin;

class Command
{
    const SUCCESS_EXIT = 0;
    const FAILURE_EXIT = 1;

    public static function run()
    {
        $opts = getopt(
            'hi:o:',
            array(
                'help',
                'input:',
                'output:',
                'chunk-length:',
                'keep-sourcemap',
                'linebreak-position:',
                'memory-limit:',
                'pcre-backtrack-limit:',
                'pcre-recursion-limit:'
            )
        );

        $help = self::getOpt(array('h', 'help'), $opts);
        $input = self::getOpt(array('i', 'input'), $opts);
        $output = self::getOpt(array('o', 'output'), $opts);
        $chunkLength = self::getOpt('chunk-length', $opts);
        $keepSourceMap = self::getOpt('keep-sourcemap', $opts);
        $linebreakPosition = self::getOpt('linebreak-position', $opts);
        $memoryLimit = self::getOpt('memory-limit', $opts);
        $backtrackLimit = self::getOpt('pcre-backtrack-limit', $opts);
        $recursionLimit = self::getOpt('pcre-recursion-limit', $opts);

        if (!is_null($help)) {
            self::showHelp();
            die(self::SUCCESS_EXIT);
        }

        if (is_null($input)) {
            fwrite(STDERR, '-i <file> argument is missing' . PHP_EOL);
            self::showHelp();
            die(self::FAILURE_EXIT);
        }

        if (!is_readable($input)) {
            fwrite(STDERR, 'Input file is not readable' . PHP_EOL);
            die(self::FAILURE_EXIT);
        }

        $css = file_get_contents($input);

        if ($css === false) {
            fwrite(STDERR, 'Input CSS code could not be retrieved from input file' . PHP_EOL);
            die(self::FAILURE_EXIT);
        }

        $cssmin = new Minifier;

        if (!is_null($keepSourceMap)) {
            $cssmin->keepSourceMap();
        }

        if (!is_null($chunkLength)) {
            $cssmin->setChunkLength($chunkLength);
        }

        if (!is_null($linebreakPosition)) {
            $cssmin->setLineBreakPosition($linebreakPosition);
        }

        if (!is_null($memoryLimit)) {
            $cssmin->setMemoryLimit($memoryLimit);
        }

        if (!is_null($backtrackLimit)) {
            $cssmin->setPcreBacktrackLimit($backtrackLimit);
        }

        if (!is_null($recursionLimit)) {
            $cssmin->setPcreRecursionLimit($recursionLimit);
        }

        $css = $cssmin->run($css);

        if (is_null($output)) {
            fwrite(STDOUT, $css . PHP_EOL);
            die(self::SUCCESS_EXIT);
        }

        if (!is_writable(dirname($output))) {
            fwrite(STDERR, 'Output file is not writable' . PHP_EOL);
            die(self::FAILURE_EXIT);
        }

        if (file_put_contents($output, $css) === false) {
            fwrite(STDERR, 'Compressed CSS code could not be saved to output file' . PHP_EOL);
            die(self::FAILURE_EXIT);
        }

        die(self::SUCCESS_EXIT);
    }

    protected function getOpt($opts, $options)
    {
        $value = null;

        if (is_string($opts)) {
            $opts = array($opts);
        }

        foreach ($opts as $opt) {
            if (array_key_exists($opt, $options)) {
                $value = $options[$opt];
                break;
            }
        }

        return $value;
    }

    protected function showHelp()
    {
        print <<<EOT
Usage: cssmin [options] -i <file> [-o <file>]
  
  -i|--input <file>              File containing uncompressed CSS code.
  -o|--output <file>             File to use to save compressed CSS code.
    
Options:
    
  -h|--help                      Prints this usage information.
  --chunk-length <length>        Sets the approximate number of characters to use when splitting the provided CSS string
                                 in chunks.
  --keep-sourcemap               Keeps the sourcemap special comment in the output.
  --linebreak-position <pos>     Splits long lines after a specific column in the output.
  --memory-limit <limit>         Sets the memory limit for this script.
  --pcre-backtrack-limit <limit> Sets the PCRE backtrack limit for this script.
  --pcre-recursion-limit <limit> Sets the PCRE recursion limit for this script.

EOT;
    }
}
