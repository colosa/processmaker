<?php

/**
 * This class is derived from the class archive, is employed to use files .bzip
 */
class BzipFile extends TarFile
{

    /**
     * This function is the constructor of the class bzip_file
     *
     * @param string $name
     * @return void
     */
    public function BzipFile($name)
    {
        $this->tar_file($name);
        $this->options['type'] = "bzip";
    }

    /**
     * This function is employed to create files .
     * bzip
     *
     * @return boolean
     */
    public function create_bzip()
    {
        if ($this->options['inmemory'] == 0) {
            $pwd = getcwd();
            chdir($this->options['basedir']);
            if ($fp = bzopen($this->options['name'], "wb")) {
                fseek($this->archive, 0);
                while ($temp = fread($this->archive, 1048576)) {
                    bzwrite($fp, $temp);
                }
                bzclose($fp);
                chdir($pwd);
            } else {
                $this->error[] = "Could not open {$this->options['name']} for writing.";
                chdir($pwd);
                return 0;
            }
        } else {
            $this->archive = bzcompress($this->archive, $this->options['level']);
        }
        return 1;
    }

    /**
     * This function open a archive of the class bzip_file
     *
     * @return void
     */
    public function open_archive()
    {
        return @bzopen($this->options['name'], "rb");
    }
}
