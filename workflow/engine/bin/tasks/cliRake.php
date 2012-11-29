<?php
/**
 * cliUpgrade.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2011 Colosa Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 *
 * @author Alexandre Rosenfeld <alexandre@colosa.com>
 * @package workflow-engine-bin-tasks
 */

CLI::taskName('build-js');
CLI::taskDescription(<<<EOT
    Generate Javascript Files

    This command should be run after any modification of javascript files in
    folder gulliver/js/*.
EOT
);
//CLI::taskOpt("minify", "If the option is enabled, performs the build only with minified files", "min", "buildmin");
CLI::taskRun(minify_javascript);

function minify_javascript($command, $args)
{
    CLI::logging("BUILD-JS\n");
    //disabling the rakefile version, until we have updated the dev environment
    //CLI::logging("Checking if rake is installed...\n");
    //$rakeFile = PROCESSMAKER_PATH . "workflow/engine/bin/tasks/Rakefile";
    //system('rake -f ' . $rakeFile);

    require_once (PATH_THIRDPARTY . 'jsmin/jsmin.php');

    $libraries = json_decode( file_get_contents ( PATH_HOME . 'engine/bin/tasks/libraries.json' ));
    //print_r($libraries);

    foreach ($libraries as $k=>$library ) {
        $build = $library->build;
        if ($build) {
            $bufferMini = "";
            $sum1 = 0;
            $sum2 = 0;
            $libName = $library->name;
            $files   = $library->libraries;
            $js_path = $library->build_js_to;
            printf ("Processing %s library:\n", $libName );
            foreach ( $files as $file ) {
                printf ( "    %-20s ", $file->name );
                $fileNameMini = PATH_TRUNK . $file->mini;
                if ($file->minify) {
                    $minify = JSMin::minify( file_get_contents( $fileNameMini ) );
                } else {
                    $minify = file_get_contents( $fileNameMini );
                }
                $bufferMini .= $minify;
                $size1 = filesize($fileNameMini);
                $size2 = strlen($minify);
                $sum1 += $size1;
                $sum2 += $size2;
                printf ("%7d -> %7d %5.2f%%\n", $size1, $size2, 100 - $size2/$size1*100) ;
            }
            if (substr($library->build_js_to ,-1) != '/') {
                $library->build_js_to .= '/';
            }
            $outputMiniFile = PATH_TRUNK . $library->build_js_to . $libName . ".js";
            file_put_contents ( $outputMiniFile, $bufferMini );
            printf ("    -------------------- -------    ------- ------\n");
            printf ("    %-20s %7d -> %7d %6.2f%%\n", $libName.'.js', $sum1, $sum2, 100-$sum2/$sum1*100) ;
            print "    $outputMiniFile\n";

        }
    }
    CLI::logging("BUILD-JS DONE\n");
}

