<?php

/**
 *
 * @package workflow.engine.classes
 */
class CLI
{
    public static $tasks = array ();
    public static $currentTask = null;

    /**
     * Adds a new task defined by it's name.
     * All other task functions will
     * remember the current task defined here.
     *
     * @param string $name name of the task, used in the command-line
     */
    public static function taskName ($name)
    {
        self::$currentTask = $name;
        self::$tasks[$name] = array (
            'name' => $name,
            'description' => null,
            'args' => array (),
            'function' => null,
            'opt' => array (
                'short' => '',
                'long' => array (),
                'descriptions' => array ()
            )
        );
    }

    /**
     * Adds a description to the current task.
     * The description should contain a
     * one-line description of the command and a few lines of text with more
     * information.
     *
     * @param string $description task description
     */
    public static function taskDescription ($description)
    {
        assert( self::$currentTask !== null );
        self::$tasks[self::$currentTask]["description"] = $description;
    }

    /**
     * Adds an argument to the current task.
     * The options will affect how it is
     * displayed in the help command. Optional will put [] in the argument and
     * multiple will put ... in the end. Arguments are displayed together with
     * the task name in the help command.
     *
     * @param string $name argument name
     */
    public static function taskArg ($name, $optional = true, $multiple = false)
    {
        assert( self::$currentTask !== null );
        self::$tasks[self::$currentTask]["args"][$name] = array ('optional' => $optional,'multiple' => $multiple
        );
    }

    /**
     * Defines short and long options as used by getopt to the current task.
     *
     * @param string $short short options
     * @param array $long long options
     */
    public static function taskOpt ($name, $description, $short, $long = null)
    {
        assert( self::$currentTask !== null );
        $opts = self::$tasks[self::$currentTask]["opt"];
        if ($short) {
            $opts['short'] .= $short;
        }
        if ($long) {
            $opts['long'][] = $long;
        }
        $opts['descriptions'][$name] = array ('short' => $short,'long' => $long,'description' => $description
        );
        self::$tasks[self::$currentTask]["opt"] = $opts;
    }

    /**
     * Defines the function to run for the current task.
     *
     * @param callback $function function to run
     */
    public static function taskRun ($function)
    {
        assert( self::$currentTask !== null );
        self::$tasks[self::$currentTask]["function"] = $function;
    }

    /**
     * Displays the help instructions.
     *
     * @param array $args if defined, the task name should be argument 0
     * @param array $opts options as returned by getopt
     */
    public static function help ($args, $opts = null)
    {
        global $argv;
        $scriptName = $argv[0];
        if (is_array($args) && count($args) > 0 ) {
            $taskName = $args[0];
        } else {
            $taskName = $args;
        }

        if (! $taskName) {
            echo "usage: processmaker <task> [options] [args]\n";
            echo "       If using Linux/UNIX, prepend './' to specify the directory: " . $scriptName . " <task> [options] [args]\n";
            echo "Type   'processmaker help <task>' for help on a specific task.";
            echo "\n\nAvailable tasks:\n";
            $tasks = array ();
            ksort( self::$tasks );
            foreach (self::$tasks as $name => $data) {
                $description = explode( "\n", $data['description'] );
                $tasks[] = "  $name";
            }
            $tasks = join( "\n", $tasks );
            echo $tasks . "\n\n";
        } else {
            $options = array();
            $tasks = array();
            ksort( self::$tasks );
            foreach (self::$tasks as $name => $data) {
                $description = explode( "\n", $data['description'] );
                $options[] = "$name";
            }
            if (!in_array($taskName, $options)) {
                echo "\nThe task does not exist \n";
                echo "Use one of the following tasks:\n";
                $tasks = array ();
                ksort( self::$tasks );
                foreach (self::$tasks as $name => $data) {
                    $description = explode( "\n", $data['description'] );
                    $tasks[] = "  $name";
                }
                $tasks = join( "\n", $tasks );
                echo $tasks . "\n\n";
            } else{
                $valid_args = array ();
                foreach (self::$tasks[$taskName]['args'] as $arg => $data) {
                    $arg = strtoupper( $arg );
                    if ($data['multiple']) {
                        $arg = "$arg...";
                    }
                    if ($data['optional']) {
                        $arg = "[$arg]";
                    }
                    $valid_args[] = $arg;
                }

            $nameHotfixFile = ($taskName == "hotfix-install")? "HOTFIX-FILE" : "";

            $valid_args = join( " ", $valid_args );
            $description = explode( "\n", self::$tasks[$taskName]['description'] );
            $taskDescription = trim( array_shift( $description ) );
            $description = trim( implode( "\n", $description ) );
            $message = <<< EOT
$taskName: {$taskDescription}
Usage: processmaker $taskName $nameHotfixFile $valid_args

  $description

EOT;
            $valid_options = array ();
            foreach (self::$tasks[$taskName]['opt']['descriptions'] as $opt => $data) {
                $optString = array ();
                if ($data['short']) {
                    $optString[] = "-{$data['short']}";
                }
                if ($data['long']) {
                    $optString[] = "--{$data['long']}";
                }
                $valid_options[] = "  " . join( ", ", $optString ) . "\n\t" . wordwrap( $data['description'], 70, "\n\t" );
            }
            $valid_options = join( "\n", $valid_options );
            if ($valid_options) {
                $message .= <<< EOT

Options:

$valid_options

EOT;
            }
            echo $message . "\n";
        }
        }
    }

    /**
     * Run the CLI task, which will check which command is specified and run it.
     */
    public static function run ()
    {
        CLI::taskName( "help" );
        CLI::taskRun( array ('self','help'
        ) );
        global $argv;
        $args = $argv;
        $cliname = array_shift( $args );
        $taskName = array_shift( $args );
        while ($taskName{0} == '-') {
            $taskName = array_shift( $args );
        }
        if (! $taskName) {
            echo self::error( "Specify a task from the list below." ) . "\n\n";
            self::help( null, null );
            return;
        }
        $taskData = null;
        foreach (self::$tasks as $name => $data) {
            if (strcasecmp( $name, $taskName ) === 0) {
                $taskData = $data;
                break;
            }
        }
        if (! $taskData) {
            echo self::error( "Command not found: '$taskName'" ) . "\n\n";
            self::help( null, null );
            return;
        }

        $short = "h" . $taskData['opt']['short'];
        $long = array_merge( array ("help"
        ), $taskData['opt']['long'] );
        $getopt = Console_Getopt::getopt2( $args, $short, $long );
        if (! is_array( $getopt )) {
            echo self::error( "Invalid options (" . $getopt->getMessage() . ")" ) . "\n\n";
            self::help( $taskName );
            return;
        }
        list ($options, $arguments) = $getopt;
        foreach ($taskData['opt']['descriptions'] as $optName => $optDescription) {
            $short = str_replace( ":", "", $optDescription['short'] );
            $long = str_replace( "=", "", $optDescription['long'] );
            $validOpts[$short] = $optName;
            $validOpts[$long] = $optName;
        }
        $taskOpts = array ();
        try {
            foreach ($options as $opt) {
                list ($optName, $optArg) = $opt;
                if ($optName === "h" || $optName === "--help") {
                    self::help( $taskName );
                    return;
                }
                if (strpos( $optName, '--' ) === 0) {
                    $optName = substr( $optName, 2 );
                }
                if (! array_key_exists( $optName, $validOpts )) {
                    throw new Exception( "option not found: $optName" );
                }
                if (array_key_exists( $validOpts[$optName], $taskOpts )) {
                    throw new Exception( "'$optName' specified more then once" );
                }
                $taskOpts[$validOpts[$optName]] = $optArg;
            }
        } catch (Exception $e) {
            echo self::error( "Invalid options: " . $e->getMessage() ) . "\n\n";
            self::help( $taskName );
            return;
        }
        try {
            call_user_func( $taskData['function'], $arguments, $taskOpts );
        } catch (Exception $e) {
            echo self::error( "\n  Error executing '$taskName':\n\n  {$e->getMessage()}\n" ) . "\n";
            global $tempDirectory;
            if (!empty($tempDirectory)) {
                G::rm_dir($tempDirectory);
            }
        }
    }

    /**
     * Returns an information colorized version of the message.
     *
     * @param string $message the message to colorize
     */
    public static function info ($message)
    {
        return pakeColor::colorize( $message, "INFO" );
    }

    /**
     * Returns a warning colorized version of the message.
     *
     * @param string $message the message to colorize
     */
    public static function warning ($message)
    {
        return pakeColor::colorize( $message, "COMMENT" );
    }

    /**
     * Returns an error colorized version of the message.
     *
     * @param string $message the message to colorize
     */
    public static function error ($message)
    {
        return pakeColor::colorize( $message, "ERROR" );
    }

    /**
     * Prompt the user for information.
     *
     * @param string $message the message to display
     * @return string the text typed by the user
     */
    public static function prompt ($message)
    {
        echo "$message";
        $handle = fopen( "php://stdin", "r" );
        $line = fgets( $handle );
        return $line;
    }

    /**
     * Ask a question of yes or no.
     *
     * @param string $message the message to display
     * @return bool true if the user choosed no, false otherwise
     */
    public static function question ($message)
    {
        $input = strtolower( self::prompt( "$message [Y/n] " ) );
        return (array_search( trim( $input ), array ("y",""
        ) ) !== false);
    }

    /**
     * Display a message to the user.
     * If filename is specified, it will setup
     * a logging file where all messages will be recorded.
     *
     * @param string $message the message to display
     * @param string $filename the log file to write messages
     */
    public static function logging ($message, $filename = null)
    {
        static $log_file = null;
        if (isset( $filename )) {
            $log_file = fopen( $filename, "a" );
            fwrite( $log_file, " -- " . date( "c" ) . " " . $message . " --\n" );
        } else {
            if (isset( $log_file )) {
                fwrite( $log_file, $message );
            }
            echo $message;
        }
    }
}
