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
 *
 * @package workflow.engine.classes
 */
class Archive
{

    /**
     * This function is the constructor of the class archive
     *
     * @param string $name
     * @return void
     *
     */
    public function archive($name)
    {
        $this->options = array('basedir' => ".", 'name' => $name, 'prepend' => "", 'inmemory' => 0, 'overwrite' => 0, 'recurse' => 1, 'storepaths' => 1, 'followlinks' => 0, 'level' => 3, 'method' => 1, 'sfx' => "", 'type' => "", 'comment' => ""
        );
        $this->files = array();
        $this->exclude = array();
        $this->storeonly = array();
        $this->error = array();
    }

    /**
     * This function gives options to a archive
     *
     * @param array $options
     * @return void
     */
    public function set_options($options)
    {
        foreach ($options as $key => $value) {
            $this->options[$key] = $value;
        }
        if (!empty($this->options['basedir'])) {
            $this->options['basedir'] = str_replace("\\", "/", $this->options['basedir']);
            $this->options['basedir'] = preg_replace("/\/+/", "/", $this->options['basedir']);
            $this->options['basedir'] = preg_replace("/\/$/", "", $this->options['basedir']);
        }
        if (!empty($this->options['name'])) {
            $this->options['name'] = str_replace("\\", "/", $this->options['name']);
            $this->options['name'] = preg_replace("/\/+/", "/", $this->options['name']);
        }
        if (!empty($this->options['prepend'])) {
            $this->options['prepend'] = str_replace("\\", "/", $this->options['prepend']);
            $this->options['prepend'] = preg_replace("/^(\.*\/+)+/", "", $this->options['prepend']);
            $this->options['prepend'] = preg_replace("/\/+/", "/", $this->options['prepend']);
            $this->options['prepend'] = preg_replace("/\/$/", "", $this->options['prepend']) . "/";
        }
    }

    /**
     * This function is used to create a archive.
     *
     * @return boolean
     */
    public function create_archive()
    {
        $this->make_list();
        if ($this->options['inmemory'] == 0) {
            $pwd = getcwd();
            chdir($this->options['basedir']);
            if ($this->options['overwrite'] == 0 && file_exists($this->options['name'] . ($this->options['type'] == "gzip" || $this->options['type'] == "bzip" ? ".tmp" : ""))) {
                $this->error[] = "File {$this->options['name']} already exist.";
                chdir($pwd);
                return 0;
            } elseif ($this->archive = @fopen($this->options['name'] . ($this->options['type'] == "gzip" || $this->options['type'] == "bzip" ? ".tmp" : ""), "wb+")) {
                chdir($pwd);
            } else {
                $this->error[] = "Could not open {$this->options['name']} for writing.";
                chdir($pwd);
                return 0;
            }
        } else {
            $this->archive = "";
        }
        switch ($this->options['type']) {
            case "zip":
                if (!$this->create_zip()) {
                    $this->error[] = "Could not create zip file.";
                    return 0;
                }
                break;
            case "bzip":
                if (!$this->create_tar()) {
                    $this->error[] = "Could not create tar file.";
                    return 0;
                }
                if (!$this->create_bzip()) {
                    $this->error[] = "Could not create bzip2 file.";
                    return 0;
                }
                break;
            case "gzip":
                if (!$this->create_tar()) {
                    $this->error[] = "Could not create tar file.";
                    return 0;
                }
                if (!$this->create_gzip()) {
                    $this->error[] = "Could not create gzip file.";
                    return 0;
                }
                break;
            case "tar":
                if (!$this->create_tar()) {
                    $this->error[] = "Could not create tar file.";
                    return 0;
                }
        }
        if ($this->options['inmemory'] == 0) {
            fclose($this->archive);
            if ($this->options['type'] == "gzip" || $this->options['type'] == "bzip") {
                unlink($this->options['basedir'] . "/" . $this->options['name'] . ".tmp");
            }
        }
    }

    /**
     * This function is used for add data to a archive
     *
     * @param string $data
     * @return void
     */
    public function add_data($data)
    {
        if ($this->options['inmemory'] == 0) {
            fwrite($this->archive, $data);
        } else {
            $this->archive .= $data;
        }
    }

    /**
     * This function make a list
     *
     * @return void
     */
    public function make_list()
    {
        if (!empty($this->exclude)) {
            foreach ($this->files as $key => $value) {
                foreach ($this->exclude as $current) {
                    if ($value['name'] == $current['name']) {
                        unset($this->files[$key]);
                    }
                }
            }
        }
        if (!empty($this->storeonly)) {
            foreach ($this->files as $key => $value) {
                foreach ($this->storeonly as $current) {
                    if ($value['name'] == $current['name']) {
                        $this->files[$key]['method'] = 0;
                    }
                }
            }
        }
        unset($this->exclude, $this->storeonly);
    }

    /**
     * Add files a list
     *
     * @param array $list
     * @return void
     */
    public function add_files($list)
    {
        $temp = $this->list_files($list);
        foreach ($temp as $current) {
            $this->files[] = $current;
        }
    }

    /**
     * This function exclude files of a list
     *
     * @param array $list
     * @return void
     */
    public function exclude_files($list)
    {
        $temp = $this->list_files($list);
        foreach ($temp as $current) {
            $this->exclude[] = $current;
        }
    }

    /**
     * This function store files
     *
     * @param array $list
     */
    public function store_files($list)
    {
        $temp = $this->list_files($list);
        foreach ($temp as $current) {
            $this->storeonly[] = $current;
        }
    }

    /**
     * List files gives a List
     *
     * @param array $list
     * @return array
     */
    public function list_files($list)
    {
        if (!is_array($list)) {
            $temp = $list;
            $list = array($temp
            );
            unset($temp);
        }
        $files = array();
        $pwd = getcwd();
        chdir($this->options['basedir']);
        foreach ($list as $current) {
            $current = str_replace("\\", "/", $current);
            $current = preg_replace("/\/+/", "/", $current);
            $current = preg_replace("/\/$/", "", $current);
            if (strstr($current, "*")) {
                $regex = preg_replace("/([\\\^\$\.\[\]\|\(\)\?\+\{\}\/])/", "\\\\\\1", $current);
                $regex = str_replace("*", ".*", $regex);
                $dir = strstr($current, "/") ? substr($current, 0, strrpos($current, "/")) : ".";
                $temp = $this->parse_dir($dir);
                foreach ($temp as $current2) {
                    if (preg_match("/^{$regex}$/i", $current2['name'])) {
                        $files[] = $current2;
                    }
                }
                unset($regex, $dir, $temp, $current);
            } elseif (@is_dir($current)) {
                $temp = $this->parse_dir($current);
                foreach ($temp as $file) {
                    $files[] = $file;
                }
                unset($temp, $file);
            } elseif (@file_exists($current)) {
                $files[] = array('name' => $current, 'name2' => $this->options['prepend'] . preg_replace("/(\.+\/+)+/", "", ($this->options['storepaths'] == 0 && strstr($current, "/")) ? substr($current, strrpos($current, "/") + 1) : $current), 'type' => @is_link($current) && $this->options['followlinks'] == 0 ? 2 : 0, 'ext' => substr($current, strrpos($current, ".")), 'stat' => stat($current)
                );
            }
        }
        chdir($pwd);
        unset($current, $pwd);
        usort($files, array("archive", "sort_files"
        ));
        return $files;
    }

    /**
     * This function is for parse a directory name
     *
     * @param string $dirname
     * @return array
     */
    public function parse_dir($dirname)
    {
        if ($this->options['storepaths'] == 1 && !preg_match("/^(\.+\/*)+$/", $dirname)) {
            $files = array(array('name' => $dirname, 'name2' => $this->options['prepend'] . preg_replace("/(\.+\/+)+/", "", ($this->options['storepaths'] == 0 && strstr($dirname, "/")) ? substr($dirname, strrpos($dirname, "/") + 1) : $dirname), 'type' => 5, 'stat' => stat($dirname)
            )
            );
        } else {
            $files = array();
        }
        $dir = @opendir($dirname);
        while ($file = @readdir($dir)) {
            $fullname = $dirname . "/" . $file;
            if ($file == "." || $file == "..") {
                continue;
            } elseif (@is_dir($fullname)) {
                if (empty($this->options['recurse'])) {
                    continue;
                }
                $temp = $this->parse_dir($fullname);
                foreach ($temp as $file2) {
                    $files[] = $file2;
                }
            } elseif (@file_exists($fullname)) {
                $files[] = array('name' => $fullname, 'name2' => $this->options['prepend'] . preg_replace("/(\.+\/+)+/", "", ($this->options['storepaths'] == 0 && strstr($fullname, "/")) ? substr($fullname, strrpos($fullname, "/") + 1) : $fullname), 'type' => @is_link($fullname) && $this->options['followlinks'] == 0 ? 2 : 0, 'ext' => substr($file, strrpos($file, ".")), 'stat' => stat($fullname)
                );
            }
        }
        @closedir($dir);
        return $files;
    }

    /**
     * This function sort two files
     *
     * @param array $a
     * @param array $b
     * @return boolean
     */
    public function sort_files($a, $b)
    {
        if ($a['type'] != $b['type']) {
            if ($a['type'] == 5 || $b['type'] == 2) {
                return -1;
            } elseif ($a['type'] == 2 || $b['type'] == 5) {
                return 1;
            } elseif ($a['type'] == 5) {
                return strcmp(strtolower($a['name']), strtolower($b['name']));
            } elseif ($a['ext'] != $b['ext']) {
                return strcmp($a['ext'], $b['ext']);
            } elseif ($a['stat'][7] != $b['stat'][7]) {
                return $a['stat'][7] > $b['stat'][7] ? -1 : 1;
            } else {
                return strcmp(strtolower($a['name']), strtolower($b['name']));
            }
        }
        return 0;
    }

    /**
     * This function download a file
     *
     * @return void
     */
    public function download_file()
    {
        if ($this->options['inmemory'] == 0) {
            $this->error[] = "Can only use download_file() if archive is in memory. Redirect to file otherwise, it is faster.";
            return;
        }
        switch ($this->options['type']) {
            case "zip":
                header("Content-Type: application/zip");
                break;
            case "bzip":
                header("Content-Type: application/x-bzip2");
                break;
            case "gzip":
                header("Content-Type: application/x-gzip");
                break;
            case "tar":
                header("Content-Type: application/x-tar");
        }
        $header = "Content-Disposition: attachment; filename=\"";
        $header .= strstr($this->options['name'], "/") ? substr($this->options['name'], strrpos($this->options['name'], "/") + 1) : $this->options['name'];
        $header .= "\"";
        header($header);
        header("Content-Length: " . strlen($this->archive));
        header("Content-Transfer-Encoding: binary");
        header("Cache-Control: no-cache, must-revalidate, max-age=60");
        header("Expires: Sat, 01 Jan 2000 12:00:00 GMT");
        print ($this->archive);
    }
}
