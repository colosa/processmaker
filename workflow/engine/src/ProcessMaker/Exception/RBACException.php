<?php

namespace ProcessMaker\Exception;

use G;

/**
 * Class PMException
 * @package ProcessMaker\Exception
 */
class RBACException extends \Exception
{
    const PM_LOGIN = '../login/login';
    const PM_403 = '/errors/error403.php';

    /**
     * RBACException constructor.
     * @param string $message
     * @param null $code
     */
    public function __construct($message, $code=NULL)
    {
        parent::__construct($message, $code);
    }

    /**
     * Displays the entire exception as a string
     * @return string
     */
    public function __toString()
    {
        switch ($this->getCode()) {
            case -1:
                G::SendTemporalMessage($this->getMessage(), 'error', 'labels');
                $message = self::PM_LOGIN;
                break;
            case -2:
                G::SendTemporalMessage($this->getMessage(), 'error', 'labels');
                $message = self::PM_LOGIN;
                break;
            case 403:
                $message = self::PM_403;
                break;
            default:
                $message = self::PM_LOGIN;
                break;
        }
        return $message;
    }

    /**
     * Returns the path to which to redirect
     * @return $this
     */
    public function getPath()
    {
        return $this;
    }
}
