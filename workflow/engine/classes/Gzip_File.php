<?php
/*--------------------------------------------------
 * TAR/GZIP/BZIP2/ZIP ARCHIVE CLASSES 2.1
 * By Devin Doucette
 * Copyright (c) 2005 Devin Doucette
 * Email: darksnoopy@shaw.ca
 *--------------------------------------------------
 * Email bugs/suggestions to darksnoopy@shaw.ca
 *--------------------------------------------------
 * This script has been created and released under
 * the GNU GPL and is free to use and redistribute
 * only if this copyright statement is not removed
 *--------------------------------------------------*/


/**
 * This class is derived of the class archive, is employed to use archives .
 * gzip
 * @package workflow.engine.classes
 */
class GzipFile extends TarFile
{

    /**
     * This function is the constructor of the class gzip_file
     *
     * @param string $name
     * @return void
     */
    public function gzip_file($name)
    {
        $this->tar_file($name);
        $this->options['type'] = "gzip";
    }

    /**
     * This function is employed to create files .
     * gzip
     *
     * @return boolean
     */
    public function create_gzip()
    {
        if ($this->options['inmemory'] == 0) {
            $pwd = getcwd();
            chdir($this->options['basedir']);
            if ($fp = gzopen($this->options['name'], "wb{$this->options['level']}")) {
                fseek($this->archive, 0);
                while ($temp = fread($this->archive, 1048576)) {
                    gzwrite($fp, $temp);
                }
                gzclose($fp);
                chdir($pwd);
            } else {
                $this->error[] = "Could not open {$this->options['name']} for writing.";
                chdir($pwd);
                return 0;
            }
        } else {
            $this->archive = gzencode($this->archive, $this->options['level']);
        }
        return 1;
    }

    /**
     * This function open a archive of the class gzip_file
     *
     * @return void
     */
    public function open_archive()
    {
        return @gzopen($this->options['name'], "rb");
    }
}
