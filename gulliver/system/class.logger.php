<?php

/**
 * class.logger.php
 * Stores a message in the log file, if the file size exceeds 
 * specified log file is renamed and a new one is created.
 * $fileName = "filename";
 * $fileExtension = ".log";
 * $fileSeparatorVersion = "_";
 * $limitSize = 1000000;
 *      10000000 -> approximately 10 megabytes
 *      1000000 -> approximately 1 megabytes
 * 
 * @author Roly Rudy Gutierrez Pinto
 * @package gulliver.system
 */
class Logger
{

    public static $instance = null;
    public $limitFile;
    public $limitSize;
    public $fileName;
    public $fileExtension;
    public $fileSeparatorVersion;
    public $path;
    private $fullName;
    private $filePath;

    public function __construct($pathData, $pathSep, $file = 'cron.log')
    {
        $this->limitFile = 5;
        $this->limitSize = 1000000;
        $filename = pathinfo($file);
        if (isset($filename['filename']) && isset($filename['extension'])) {
            $this->fileName = $filename['filename'];
            $this->fileExtension = '.' . $filename['extension'];
        } else {
            $this->fileName = 'cron';
            $this->fileExtension = '.log';
        }
        $this->fileSeparatorVersion = "_";
        $this->path = $pathData . "log" . $pathSep;

        $this->fullName = $this->fileName . $this->fileExtension;
        $this->filePath = $this->path . $this->fullName;
    }

    public function getSingleton($pathData, $pathSep, $file = 'cron.log')
    {
        if (self::$instance == null) {
            self::$instance = new Logger($pathData, $pathSep, $file);
        }
        return self::$instance;
    }

    private function getNumberFile($file)
    {
        $number = str_replace($this->fileExtension, "", $file);
        $number = str_replace($this->fileName, "", $number);
        $number = str_replace($this->fileSeparatorVersion, "", $number);
        return $number;
    }

    private function isFileLog($file)
    {
        return !is_dir($file) &&
                strpos($file, $this->fileExtension) !== false &&
                strpos($file, $this->fileName . $this->fileSeparatorVersion) !== false;
    }

    private function renameFile()
    {
        clearstatcache();
        if (file_exists($this->filePath)) {
            $size = filesize($this->filePath);
            if ($size >= $this->limitSize) {
                $dir = opendir($this->path);
                $ar = array();
                while ($file = readdir($dir)) {
                    if ($this->isFileLog($file)) {
                        $number = $this->getNumberFile($file);
                        array_push($ar, $number);
                    }
                }
                sort($ar);
                $n = count($ar);
                for ($i = $n - 1; $i >= 0; $i--) {
                    $oldName = $this->path . $this->fileName . $this->fileSeparatorVersion . ($ar[$i]) . $this->fileExtension;
                    $newName = $this->path . $this->fileName . $this->fileSeparatorVersion . ($i + 2) . $this->fileExtension;
                    if ($i + 1 >= $this->limitFile) {
                        unlink($oldName);
                    } else {
                        rename($oldName, $newName);
                    }
                }
                rename($this->filePath, $this->path . $this->fileName . $this->fileSeparatorVersion . 1 . $this->fileExtension);
            }
        }
    }

    public function write($message)
    {
        $this->renameFile();
        $file = fopen($this->filePath, "a+");
        $message = date('Y-m-d H:i:s') . $message;
        fwrite($file, $message);
        fclose($file);
    }
}

