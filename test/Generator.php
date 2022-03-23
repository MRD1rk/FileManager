<?php

namespace test;

class Generator
{
    private string $outputPath = __DIR__.'/../output/';
    private int $fileCount;
    private int $maxDepth;
    private int $maxLengthName;
    private int $maxFileSize;
    private int $probability = 0;
    private int $chunkSize = 1024;// 1K
    private array $generatedFiles = [];
    private string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';


    public function __construct($settings = [])
    {
        if (empty($settings)) {
            $this->loadDefaultSettings();
        }
        if (!file_exists($this->outputPath)) {
            mkdir($this->outputPath,0700,true);
        }
    }

    /**
     * Load default settings for Generator
     */
    private function loadDefaultSettings()
    {
        $this->setFileCount(6)
            ->setMaxDepth(3)
            ->setMaxLengthName(12)
            ->setProbability(50)
            ->setMaxFileSize('1M');
    }

    /**
     * @param $fileCount
     * @return $this
     */
    public function setFileCount($fileCount)
    {
        $this->fileCount = $fileCount;
        return $this;
    }

    /**
     * @param $maxDepth
     * @return $this
     */
    public function setMaxDepth($maxDepth)
    {
        $this->maxDepth = $maxDepth;
        return $this;
    }

    /**
     * Calculate fileSize in bytes
     * @param $maxFileSize
     * @return $this
     */
    public function setMaxFileSize($maxFileSize)
    {
        $bytes = null;
        $template = '/([\d]+)([MKG]{1})/';
        preg_match($template, $maxFileSize, $matches);
        $size = $matches[1];
        $label = $matches[2];
        switch ($label) {
            case 'K':
                $bytes = 1024 * $size;
                break;
            case 'M':
                $bytes = pow(1024, 2) * $size;
                break;
            case 'G':
                $bytes  = pow(1024, 3) * $size;
                break;
        }
        $this->maxFileSize = $bytes;
        return $this;
    }

    /**
     * @param $maxLengthName
     * @return $this
     */
    public function setMaxLengthName($maxLengthName)
    {
        $this->maxLengthName = $maxLengthName;
        return $this;
    }

    /**
     * @param $probability
     * @return $this
     */
    public function setProbability($probability) {
        $this->probability = $probability;
        return $this;
    }

    /**
     * Main function
     */
    public function process() :void
    {
        for ($i = 0;$i < $this->fileCount; $i++) {
            $name = $this->generateName();
            $this->createFile($name);
        }
        $this->markSimilarFiles();
        foreach ($this->generatedFiles as $generatedFile) {
            $this->loadData($generatedFile);
        }
    }

    /**
     * Save similar content
     */
    public function markSimilarFiles()
    {
        $probability = $this->probability;
        $countSimilar = (int) ceil($this->fileCount * ($probability / 100));
        if ($countSimilar > 1) {
            $similarContent = $this->generateContent($this->chunkSize);
            shuffle($this->generatedFiles);//shuffle files
            for ($i = 0; $i < $countSimilar;$i++) {
                $this->generatedFiles[$i]['similar'] = $similarContent;
            }
        }

    }

    /**
     * @param $name
     * Create dir/file by pathname
     */
    public function createFile($name)
    {
        $directories = explode( '/', $name);
        $fileName = array_pop( $directories);
        $path = implode('/', $directories);
        $fullPath = $this->outputPath . $path;
        if (!file_exists($fullPath)){
            mkdir($fullPath, 0700, true);
        }
        file_put_contents($fullPath . '/' . $fileName, '');
        $this->generatedFiles[] = [
            'fileName' => $fullPath . '/' . $fileName,
            'similar' => false
        ];

    }

    /**
     * Load random data in file
     * @param $file
     */
    public function loadData($file)
    {
        $fileName = $file['fileName'];
        $similarContent = $file['similar'];
        if ($similarContent) {
            file_put_contents($fileName, $similarContent, FILE_APPEND);
        }
        $currentSize = 0;
        while($currentSize < $this->maxFileSize) {
            $currentSize += file_put_contents($fileName, $this->generateContent(), FILE_APPEND);
        }
    }

    /**
     * Generate content by length
     * @param int $length
     * @return string
     */
    public function generateContent($length = 0)
    {
        if (!$length) {
            $length = (int)ceil($this->maxFileSize / 100);
        }
        return $this->getRandomString($length);
    }

    /**
     * Function for generate name for files/folders
     * @return string
     */
    public function generateName() : string
    {
        $result = [];
        $maxDepth = $this->maxDepth;
        $maxSeparators = rand(0, $maxDepth - 1);
        for ($i = 0; $i <= $maxSeparators; $i++) {
            $name =  $this->getRandomString(rand(1,$this->maxLengthName));
            $result[] = $name;
        }
        return implode('/', $result);
    }

    /**
     * @param $length
     * @return string
     * @throws \Exception
     */
    public function getRandomString($length): string
    {
        $parts = [];
        $max = strlen($this->keyspace) - 1;
        for ($i = 0;$i < $length; $i++) {
            $parts[] = $this->keyspace[random_int(0, $max)];
        }
        return implode('', $parts);
    }
}