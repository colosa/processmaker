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

/**
 * The ProcessMaker memcached class
 *
 * @package workflow.engine.ProcessMaker
 */

class PMmemcached
{
    const ONE_MINUTE = 60;
    const ONE_HOUR = 3600;
    const TWO_HOURS = 7200;
    const EIGHT_HOURS = 28800;

    var $version;
    var $mem;
    var $connected = false;
    var $enabled = false;
    var $supported = false;

    private static $instance = null;

    public function __construct ($workspace)
    {
        $this->enabled = MEMCACHED_ENABLED;
        $this->connected = false;
        $this->workspace = $workspace;
        if (class_exists( 'Memcached' )) {
            $this->mem = new Memcached();
            $this->class = 'Memcached';
            $this->connected = true;
        } else {
            if (class_exists( 'Memcache' )) {
                $this->mem = new Memcache();
                $this->class = 'Memcache';
                $this->supported = true;
                $this->connected = @$this->mem->connect( MEMCACHED_SERVER, 11211 );
                if ($this->connected) {
                    $this->version = $this->mem->getVersion();
                }
            } else {
                require_once ('classes/class.fileCache.php');
                // create cache folder
                $cacheFolder = PATH_DATA . "sites/" . $workspace . "/cachefiles/";
                if (! file_exists( $cacheFolder )) {
                    if (! mkdir( $cacheFolder )) {
                        return false;
                    }
                }
                $this->class = 'fileCache';
                $this->connected = true;
                $this->mem = new FileCache( $cacheFolder );
            }
        }

        if (! MEMCACHED_ENABLED) {
            $this->connected = false;
            return false;
        }

    }

    /**
     * to get singleton instance
     *
     * @access public
     * @return object
     */
    public static function getSingleton ($workspace)
    {
        if (! self::$instance instanceof self) {
            self::$instance = new PMmemcached( $workspace );
        }
        return self::$instance;
    }

    public function __clone ()
    {
        throw new Exception( "Clone is not allowed." );
    }

    public function __wakeup ()
    {
        throw new Exception( "Deserializing is not allowed." );
    }

    public function set ($key, $object, $timeout = 0)
    {
        if (! $this->connected) {
            return false;
        }
        if ($this->class != 'filecache') {
            $this->mem->set( $this->workspace . '_' . $key, $object, false, $timeout );
        } else {
            $this->mem->set( $this->workspace . '_' . $key, $object );
        }
    }

    public function get ($key)
    {
        if (! $this->connected) {
            return false;
        }
        return $this->mem->get( $this->workspace . '_' . $key );
    }

    public function add ($key, $value)
    {
        if ((! $this->connected) || ($this->class == 'filecache')) {
            return false;
        }
        return $this->mem->add( $this->workspace . '_' . $key, $value );
    }

    public function increment ($key, $value)
    {
        if ((! $this->connected) || ($this->class == 'filecache')) {
            return false;
        }
        return $this->mem->increment( $this->workspace . '_' . $key, $value );
    }

    public function delete ($key)
    {
        if ((! $this->connected) || ($this->class == 'filecache')) {
            return false;
        }
        return $this->mem->delete( $this->workspace . '_' . $key );
    }

    public function flush ()
    {
        if ((! $this->connected) || ($this->class == 'filecache')) {
            return false;
        }
        return $this->mem->flush();
    }

    public function getStats ()
    {
        if ((! $this->connected) || ($this->class == 'filecache')) {
            return false;
        }
        return $status = $this->mem->getStats();
    }

    public function printDetails ()
    {
        if ((! $this->connected) || ($this->class == 'filecache')) {
            return false;
        }
        $status = $this->mem->getStats();
        echo "<table border='1'>";
        echo "<tr><td>Memcache Server version:</td><td> " . $status["version"] . "</td></tr>";
        echo "<tr><td>Number of hours this server has been running </td><td>" . ($status["uptime"] / 3660) . "</td></tr>";
        echo "<tr><td>Total number of items stored by this server ever since it started </td><td>" . $status["total_items"] . "</td></tr>";
        echo "<tr><td>Number of open connections </td><td>" . $status["curr_connections"] . "</td></tr>";
        echo "<tr><td>Total number of connections opened since the server started running </td><td>" . $status["total_connections"] . "</td></tr>";
        echo "<tr><td>Number of connection structures allocated by the server </td><td>" . $status["connection_structures"] . "</td></tr>";
        echo "<tr><td>Cumulative number of retrieval requests </td><td>" . $status["cmd_get"] . "</td></tr>";
        echo "<tr><td> Cumulative number of storage requests </td><td>" . $status["cmd_set"] . "</td></tr>";

        $percCacheHit = ((real) $status["get_hits"] / (real) $status["cmd_get"] * 100);
        $percCacheHit = round( $percCacheHit, 3 );
        $percCacheMiss = 100 - $percCacheHit;

        echo "<tr><td>Number of keys that have been requested and found present </td><td>" . $status["get_hits"] . " ($percCacheHit%)</td></tr>";
        echo "<tr><td>Number of items that have been requested and not found </td><td>" . $status["get_misses"] . "($percCacheMiss%)</td></tr>";

        $MBRead = (real) $status["bytes_read"] / (1024 * 1024);

        echo "<tr><td>Total number of bytes read by this server from network </td><td>" . $MBRead . " Mega Bytes</td></tr>";
        $MBWrite = (real) $status["bytes_written"] / (1024 * 1024);
        echo "<tr><td>Total number of bytes sent by this server to network </td><td>" . $MBWrite . " Mega Bytes</td></tr>";
        $MBSize = (real) $status["limit_maxbytes"] / (1024 * 1024);
        echo "<tr><td>Number of bytes this server is allowed to use for storage.</td><td>" . $MBSize . " Mega Bytes</td></tr>";
        echo "<tr><td>Number of valid items removed from cache to free memory for new items.</td><td>" . $status["evictions"] . "</td></tr>";
        echo "</table>";
    }
}

