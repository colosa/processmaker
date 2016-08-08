<?php

/**
 * class.log.php
 * @author roly
 * @package gulliver.system
 *
 */
class Log
{

    public $limitFile;
    public $limitSize;
    private $reIndex;
    public $fileName;
    public $fileExtension;
    public $fileSeparatorVersion;
    public $path;
    private $fullName;
    private $filePath;

    public function Log($type = 'cron')
    {
        $this->limitFile = 5;
        $this->limitSize = 1000;
        $this->reIndex = true;
        $this->fileName = "cron";
        $this->fileExtension = ".log";
        $this->fileSeparatorVersion = "_";
        $this->path = PATH_DATA . "log" . PATH_SEP;

        $this->fullName = $this->fileName . $this->fileExtension;
        $this->filePath = $this->path . $this->fullName;
    }

    private function getNumberFile($archivo)
    {
        $number = str_replace($this->fileExtension, "", $archivo);
        $number = str_replace($this->fileName, "", $number);
        $number = str_replace($this->fileSeparatorVersion, "", $number);
        return $number;
    }

    private function isFileLog($archivo)
    {
        return !is_dir($archivo) &&
                strpos($archivo, $this->fileExtension) !== false &&
                strpos($archivo, $this->fileName . $this->fileSeparatorVersion) !== false;
    }

    private function renameFile()
    {
        clearstatcache();
        if (file_exists($this->filePath)) {
            $size = filesize($this->filePath);
            if ($size >= $this->limitSize) {
                $directorio = opendir($this->path);
                $mayor = 0;
                while ($archivo = readdir($directorio)) {
                    if ($this->isFileLog($archivo)) {
                        $number = $this->getNumberFile($archivo);
                        if ($number > $mayor) {
                            $mayor = $number;
                        }
                    }
                }
                $c = $mayor + 1;
                rename($this->filePath, $this->path . $this->fileName . $this->fileSeparatorVersion . $c . $this->fileExtension);
            }
        }
    }

    private function reIndexFile($ar)
    {
        $m = count($ar);
        for ($i = 0; $i < $m; $i++) {
            $oldname = $this->path . $this->fileName . $this->fileSeparatorVersion . $ar[$i] . $this->fileExtension;
            $ar[$i] = (int) $ar[$i] - 1;
            $newname = $this->path . $this->fileName . $this->fileSeparatorVersion . $ar[$i] . $this->fileExtension;
            rename($oldname, $newname);
        }
        return $ar;
    }

    private function verifyLimitFile()
    {
        $ar = array();
        $directorio = opendir($this->path);
        while ($archivo = readdir($directorio)) {
            if ($this->isFileLog($archivo)) {
                $number = $this->getNumberFile($archivo);
                array_push($ar, $number);
            }
        }
        sort($ar);
        while (count($ar) >= $this->limitFile + 1) {
            unlink($this->path . $this->fileName . $this->fileSeparatorVersion . $ar[0] . $this->fileExtension);
            array_shift($ar);
            if ($this->reIndex) {
                $ar = $this->reIndexFile($ar);
            }
        }
    }

    public function write($message)
    {
        $this->renameFile();
        $this->verifyLimitFile();
        //$this->verifyLimitFile();
        $file = fopen($this->filePath, "a+");
        fwrite($file, $message);
        fclose($file);
    }

}

?>
