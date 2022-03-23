<?php

namespace test;

class Cleaner
{
    function cleanDir($dir)
    {
        if (is_dir($dir)) {
            $elements = scandir($dir);
            foreach ($elements as $element) {
                if ($element != "." && $element != "..") {
                    if (is_dir($dir . DIRECTORY_SEPARATOR . $element) && !is_link($dir . "/" . $element)) {
                        $this->cleanDir($dir . DIRECTORY_SEPARATOR . $element);
                    } else {
                        unlink($dir . DIRECTORY_SEPARATOR . $element);
                    }
                }
            }
            rmdir($dir);
        }
    }
}