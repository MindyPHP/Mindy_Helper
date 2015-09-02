<?php

namespace Mindy\Helper;

use Closure;
use Goodby\CSV\Import\Standard\Interpreter;
use Goodby\CSV\Import\Standard\Lexer;
use Goodby\CSV\Import\Standard\LexerConfig;
use Mindy\Helper\Traits\Accessors;
use Mindy\Helper\Traits\Configurator;

/**
 * Class Csv
 * @package Mindy\Helper
 */
class Csv
{
    use Configurator, Accessors;

    /**
     * @var string csv delimiter
     */
    public $delimiter = ";";

    /**
     * @var string enclosure
     */
    public $enclosure = '"';

    /**
     * @var string line end
     */
    public $lineEnd = "\r\n";

    /**
     * @var string convert from charset
     */
    public $fromCharset = "cp1251";
    /**
     * @var string convert to charset
     */
    public $toCharset = "UTF-8";

    /**
     * @param $path string absolute path to file
     * @param $closure \Closure
     * @void
     */
    public function parse($path, Closure $closure)
    {
        $config = new LexerConfig();
        $config->setDelimiter($this->delimiter);
        $config->setFromCharset($this->fromCharset);
        $config->setToCharset($this->toCharset);

        $lexer = new Lexer($config);
        $interpreter = new Interpreter();
        $interpreter->addObserver($closure);
        $lexer->parse($path, $interpreter);
    }

    /**
     * @param null $value string value to record in csv
     * @return mixed|null|string value with delimiter
     */
    public function enclose_value($value = null)
    {
        if ($value !== null && $value != '') {
            $delimiter = preg_quote($this->delimiter, '/');
            $enclosure = preg_quote($this->enclosure, '/');
            if (preg_match("/" . $delimiter . "|" . $enclosure . "|\n|\r/i", $value) || ($value{0} == ' ' || substr($value, -1) == ' ')) {
                $value = str_replace($this->enclosure, $this->enclosure . $this->enclosure, $value);
                $value = $this->enclosure . $value . $this->enclosure;
            }
        }
        return $value;
    }


    public function createCsv($header, $data, $filePath, $inCharset='UTF-8')
    {
        if (!file_exists($filePath)) {
            file_put_contents($filePath, '');
        }

        function iterate(array $tmp)
        {
            foreach ($tmp as $row) {
                yield $row;
            }
        }

        $data = array_merge([$header], $data);
        foreach (iterate($data) as $row) {
            $line = [];
            foreach ($row as $attribute) {
                $value = iconv($inCharset, 'cp1251', $attribute);
                $line[] = $this->enclose_value($value);
            }
            $this->putRow($line, $filePath);
        }


    }

    protected function putRow(array $row, $filePath)
    {
        $current = file_get_contents($filePath);
        $current .= implode($this->delimiter, $row) . $this->lineEnd;
        file_put_contents($filePath, $current);
    }


}
