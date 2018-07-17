<?php

/**
 * XMLResult
 *
 * @package workflow.engine.ProcessMaker
 */
class XMLResult
{
    var $result = array ();
    var $cursor = 0;

    /**
     * XMLResult
     *
     * @param array $result
     * @return void
     */
    public function XMLResult ($result = array())
    {
        $this->result = $result;
        $this->cursor = 0;
    }

    /**
     * numRows
     *
     * @return integer sizeof($this->result)
     */
    public function numRows ()
    {
        return sizeof( $this->result );
    }

    /**
     * fetchRow
     *
     * @param string $const
     * @return integer $this->result[ $this->cursor-1 ];
     */
    public function fetchRow ($const)
    {
        if ($this->cursor >= $this->numRows()) {
            return null;
        }
        $this->cursor ++;
        return $this->result[$this->cursor - 1];
    }
}
