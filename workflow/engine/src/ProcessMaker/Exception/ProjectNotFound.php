<?php
namespace ProcessMaker\Exception;

use ProcessMaker\Project;

class ProjectNotFound extends \RuntimeException
{
    const EXCEPTION_CODE = 400;

    public function __construct(Project\Handler $obj, $uid, $message = "", \Exception $previous = null) {
        $message = empty($message) ? sprintf("Project \"%s\" with UID: %s, does not exist.", get_class($obj), $uid) : $message;

        parent::__construct($message, self::EXCEPTION_CODE, $previous);
    }

    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}