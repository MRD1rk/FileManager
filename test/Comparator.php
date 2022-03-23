<?php

namespace test;

class Comparator
{
    private string $dir;
    private array $files = [];
    
    public function __construct(string $dir)
    {
        $this->dir = $dir;
    }

    public function process()
    {
        $this->findFilesByDir($this->dir);
        $result  = [];
        $lines = [];
        echo '<pre>';
        foreach ($this->files as $index => $file) {
            foreach ($this->readFile($file['fileName']) as $line) {
                $lines[$index][] = hash('sha1', $line) ;
//                $lines[$index][] =$line ;
            }
        }
        $lines = $this->check($lines);
        $lines = array_filter($lines, function ($v) {
            return count($v) > 1;
        });
        foreach (current($lines) as $line) {
            $result[] = $this->files[$line]['fileName'];
        }

        var_dump($result);
    }

    public function check($array)
    {
        $result = [];
        foreach ($array as $fileIndex => $item) {
            foreach ($item as $hash) {
                $result[$hash][] = $fileIndex;
            }
        }
        return $result;
    }
    public function findFilesByDir($dir)
    {
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir), \RecursiveIteratorIterator::SELF_FIRST );
        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isFile()) {
                $this->files[] = [
                    'fileName' => $fileInfo->getPathname(),
                    'size' => $fileInfo->getSize()
                ];
            }
        }
    }

    public function readFile($path)
    {
            $file = fopen($path, 'r');

            if (!$file)
                die('file does not exist or cannot be opened');

            while (($line = fgets($file,1024)) !== false) {
                yield $line;
            }

            fclose($file);
    }
}