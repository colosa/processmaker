<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
//
// +----------------------------------------------------------------------+
// | PHP version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Author: Marcelo Subtil Marcal <jason@conectiva.com.br>               |
// +----------------------------------------------------------------------+
//
// $Id: lang.pt_BR.php,v 1.6 2003/09/29 12:23:58 makler Exp $
//
// Numbers_Words class extension to spell numbers in Brazilian Portuguese language.
//


/**
 * Class for translating numbers into Brazilian Portuguese.
 *
 * @author Marcelo Subtil Marcal <jason@conectiva.com.br>
 * @package Numbers_Words
 */

/**
 * Include needed files
 */
require_once "Numbers/Words.php";

/**
 * Class for translating numbers into Brazilian Portuguese.
 *
 * @author Marcelo Subtil Marcal <jason@conectiva.com.br>
 * @package Numbers_Words
 */
class Numbers_Words_pt_BR extends Numbers_Words
{

    /**
     * Locale name
     * @var string
     * @access public
     */
    var $locale      = 'pt_BR';

    /**
     * Language name in English
     * @var string
     * @access public
     */
    var $lang        = 'Brazilian Portuguese';

    /**
     * Native language name
     * @var string
     * @access public
     */
    var $lang_native = 'Português Brasileiro';

    /**
     * The word for the minus sign
     * @var string
     * @access private
     */
    var $_minus = 'menos';

    /**
     * The word separator
     * @var string
     * @access private
     */
    var $_sep = ' ';

    /**
     * The array containing the digits (indexed by the digits themselves).
     * @var array
     * @access private
     */
    var $_unidade = array(
        '',
        'um',
        'dois',
        'três',
        'quatro',
        'cinco',
        'seis',
        'sete',
        'oito',
        'nove'
    );

    /**
     * The array containing numbers 10-19.
     * @var array
     * @access private
     */
    var $_dezena10 = array(
        'dez',
        'onze',
        'doze',
        'treze',
        'quatorze',
        'quinze',
        'dezesseis',
        'dezessete',
        'dezoito',
        'dezenove'
    );

    /**
     * The array containing numbers for 10,20,...,90.
     * @var array
     * @access private
     */
    var $_dezena = array(
        '',
        'dez',
        'vinte',
        'trinta',
        'quarenta',
        'cinquenta',
        'sessenta',
        'setenta',
        'oitenta',
        'noventa'
    );

    /**
     * The array containing numbers for hundrets.
     * @var array
     * @access private
     */
    var $_centena = array(
        '',
        'cem',
        'duzentos',
        'trezentos',
        'quatrocentos',
        'quinhentos',
        'seiscentos',
        'setecentos',
        'oitocentos',
        'novecentos'
    );

    /**
     * The sufixes for exponents (singular and plural)
     * @var array
     * @access private
     */
    var $_expoente = array(
        '',
        'mil',
        'milhão',
        'bilhão',
        'trilhão',
        'quatrilhão',
        'quintilhão',
        'sextilhão',
        'setilhão',
        'octilhão',
        'nonilhão',
        'decilhão',
        'undecilhão',
        'dodecilhão',
        'tredecilhão',
        'quatuordecilhão',
        'quindecilhão',
        'sedecilhão',
        'septendecilhão'
    );



    /**
     * Converts a number to its word representation
     * in Brazilian Portuguese language
     *
     * @param  integer $num   An integer between -infinity and infinity inclusive :)
     *                        that need to be converted to words
     *
     * @return string  The corresponding word representation
     *
     * @access public
     * @author Marcelo Subtil Marcal <jason@conectiva.com.br>
     * @since  PHP 4.2.3
     */
    function toWords($num) {

        $ret = '';

        $num = trim($num);

        if (substr($num, 0, 1) == '-') {
            $ret = $this->_sep . $this->_minus;
            $num = substr($num, 1);
        }

        // strip excessive zero signs and spaces
        $num = trim($num);
        $num = preg_replace('/^0+/','',$num);

        while (strlen($num) % 3 != 0) {
            $num = "0" . $num;
        }

        $num = ereg_replace("(...)", "\\1.", $num);
        $num = ereg_replace("\.$", "", $num);

        $inteiro = explode(".", $num);

        for ($i = 0; $i < count($inteiro); $i++) {
            $ret .= (($inteiro[$i] > 100) && ($inteiro[$i] < 200)) ? "cento" : $this->_centena[$inteiro[$i][0]];
            $ret .= ($inteiro[$i][0] && ($inteiro[$i][1] || $inteiro[$i][2])) ? " e " : "";
            $ret .= ($inteiro[$i][1] < 2) ? "" : $this->_dezena[$inteiro[$i][1]];
            $ret .= (($inteiro[$i][1] > 1) && ($inteiro[$i][2])) ? " e " : "";
            $ret .= ($inteiro > 0) ? ( ($inteiro[$i][1] == 1) ? $this->_dezena10[$inteiro[$i][2]] : $this->_unidade[$inteiro[$i][2]] ) : "";
            $ret .= $inteiro[$i] > 0 ? " " . ($inteiro[$i] > 1 ? str_replace("ão", "ões", $this->_expoente[count($inteiro)-1-$i]) : $this->_expoente[count($inteiro)-1-$i]) : "";

            if ($ret && (isset($inteiro[$i+1]))) {
                if ($inteiro[$i+1] != "000") {
                    $ret .= ($i+1) == (count($inteiro)-1) ? " e " : ", ";
                }
            }

        }

        return $ret ? " $ret" : " zero";

    }
}

?>
