<?php

/**
 * XMLDB
 *
 */
class XMLDB
{

    /**
     * &connect
     *
     * @param string $dsn
     * @return array $options
     */
    public function &connect ($dsn, $options = array())
    {
        //Needed for $mysql_real_escape_string
        $mresdbc = new DBConnection();

        if (! file_exists( $dsn )) {
            $err = new DB_Error( "File $dsn not found." );
            return $err;
        }
        $dbc = new XMLConnection( $dsn );
        return $dbc;
    }

    /**
     * isError
     *
     * @param string $result
     * @return boolean is_a($result, 'DB_Error')
     */
    public function isError ($result)
    {
        return is_a( $result, 'DB_Error' );
    }
}
