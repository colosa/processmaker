<?php
/**
 * class.memcached.php
 *
 * @package workflow.engine.ProcessMaker
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2011 Colosa Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 *
 */


class FileCache
{
    function __construct ($dir)
    {
        $this->dir = $dir;
    }

    private function _name($key)
    {
        return sprintf("%s%s", $this->dir, sha1($key));
    }

    public function get ($key, $expiration = 3600)
    {

        if (! is_dir( $this->dir ) or ! is_writable( $this->dir )) {
            return FALSE;
        }

        $cache_path = $this->_name( $key );

        if (! @file_exists( $cache_path )) {
            return FALSE;
        }

        if (filemtime( $cache_path ) < (time() - $expiration)) {
            // $this->delete($key);
            // different users can have different timeout requests
            return FALSE;
        }

        if (! $fp = @fopen( $cache_path, 'rb' )) {
            return FALSE;
        }

        flock( $fp, LOCK_SH );

        $cache = '';

        if (filesize( $cache_path ) > 0) {
            $cache = unserialize( fread( $fp, filesize( $cache_path ) ) );
        } else {
            $cache = NULL;
        }

        flock( $fp, LOCK_UN );
        fclose( $fp );

        return $cache;
    }

    public function set ($key, $data)
    {

        if (! is_dir( $this->dir ) or ! is_writable( $this->dir )) {
            return FALSE;
        }

        $cache_path = $this->_name( $key );

        if (! $fp = fopen( $cache_path, 'wb' )) {
            return FALSE;
        }

        if (flock( $fp, LOCK_EX )) {
            fwrite( $fp, serialize( $data ) );
            flock( $fp, LOCK_UN );
        } else {
            return FALSE;
        }
        fclose( $fp );
        @chmod( $cache_path, 0777 );
        return TRUE;
    }

    public function delete($key)
    {
        $cache_path = $this->_name($key);

        if (file_exists($cache_path)) {
            unlink($cache_path);
            return true;
        }

        return false;
    }

    public function flush()
    {
        G::rm_dir($this->dir);
    }

    public function getStats()
    {
        return null;
    }
}
