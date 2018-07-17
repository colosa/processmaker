<?php

try {
    $filter = new InputFilter();
    $_POST = $filter->xssFilterHard($_POST);

    if (isset($_POST['form'])) {
        $_POST = $_POST['form'];
    }
    $_POST['function'] = get_ajax_value('function');
    $_POST['function'] = $filter->xssFilterHard($_POST['function']);
    switch ($_POST['function']) {
        case 'savePredetermined':
            $tranlationsList = Translation::getTranslationEnvironments();
            G::pr($tranlationsList);
            if (isset($meta['LAN_ID']) && $meta['LAN_ID'] == $_POST['lang']) {
                echo 'The Setting was saved successfully!';
            } else {
                echo 'Some error occured while the setting was being save, try later please.';
            }
            break;
        case 'languagesList':
            $isoCountry = new IsoCountry();
            $translationRow = new Translation();
            $response = new stdClass();
            $translationsEnvList = $translationRow->getTranslationEnvironments();
            $i = 0;
            foreach ($translationsEnvList as $locale => $translationRow) {
                $countryId = $translationRow['IC_UID'];
                if ($countryId != '') {
                    $isoCountryRecord = $isoCountry->findById(strtoupper($countryId));
                    $flag = strtolower($isoCountryRecord['IC_UID']);
                    $countryName = $translationRow['HEADERS']['X-Poedit-Country'];
                } else {
                    $flag = 'international';
                    $countryName = G::LoadTranslation('ID_INTERNATIONAL');
                }

                $conf = new Configurations();
                $confCasesList = $conf->getConfiguration('casesList', 'todo');

                if (isset($confCasesList['dateformat'])) {
                    $datetime = explode(' ', $translationRow['DATE']);
                    $date = explode('-', $datetime[0]);
                    if (count($datetime) == 2) {
                        $time = explode(':', $datetime[1]);
                    }

                    if (count($date) == 3) {
                        if (count($time) >= 2) {
                            $dateFormat = date($confCasesList['dateformat'],
                                mktime($time[0], $time[1], 0, $date[1], $date[2], $date[0]));
                        } else {
                            $dateFormat = date($confCasesList['dateformat'], mktime(0, 0, 0, $date[1], $date[2], $date[0]));
                        }
                    } else {
                        $dateFormat = $translationRow['DATE'];
                    }

                    $datetime = explode(' ', $translationRow['HEADERS']['PO-Revision-Date']);

                    $date = explode('-', $datetime[0]);
                    if (count($datetime) == 2) {
                        $time = explode(':', $datetime[1]);
                    }

                    if (count($date) == 3) {
                        if (count($time) >= 2) {
                            $revDate = date($confCasesList['dateformat'],
                                mktime($time[0], substr($time[1], 0, 2), 0, $date[1], $date[2], $date[0]));
                        } else {
                            $revDate = date($confCasesList['dateformat'],
                                mktime(0, 0, 0, $date[1], $date[2], $date[0]));
                        }
                    } else {
                        $revDate = $translationRow['HEADERS']['PO-Revision-Date'];
                    }
                } else {
                    $dateFormat = $translationRow['DATE'];
                    $revDate = $translationRow['HEADERS']['PO-Revision-Date'];
                }

                $languagesList[$i]['LAN_ID'] = $translationRow['LAN_ID'];
                $languagesList[$i]['LOCALE'] = $translationRow['LOCALE'];
                $languagesList[$i]['LAN_FLAG'] = $flag;
                $languagesList[$i]['NUM_RECORDS'] = $translationRow['NUM_RECORDS'];
                $languagesList[$i]['DATE'] = $dateFormat;
                $languagesList[$i]['LAN_NAME'] = $translationRow['HEADERS']['X-Poedit-Language'];
                $languagesList[$i]['COUNTRY_NAME'] = $countryName;
                $languagesList[$i]['TRANSLATOR'] = htmlentities($translationRow['HEADERS']['Last-Translator']);
                $languagesList[$i]['REV_DATE'] = $revDate;
                $languagesList[$i]['VERSION'] = $translationRow['HEADERS']['Project-Id-Version'];

                $i++;
            }
            $translationRow = new Translation();
            $response->data = $languagesList;
            print (G::json_encode($response));
            break;
        case 'delete':
            include_once 'classes/model/Translation.php';
            include_once 'classes/model/Content.php';
            $locale = $_POST['LOCALE'];
            $trn = new Translation();

            if (strpos($locale, Translation::$localeSeparator)) {
                list ($LAN_ID, $IC_UID) = explode(Translation::$localeSeparator, $locale);
            }

            //Verify if is the default language 'en'
            if ($locale != "en") {
                //Verify if is the current language
                if ($locale != SYS_LANG) {
                    try {
                        Content::removeLanguageContent($locale);
                        $trn->removeTranslationEnvironment($locale);
                        echo G::LoadTranslation('ID_LANGUAGE_DELETED_SUCCESSFULLY');
                    } catch (Exception $e) {
                        $token = strtotime("now");
                        PMException::registerErrorLog($e, $token);
                        G::outRes(G::LoadTranslation("ID_EXCEPTION_LOG_INTERFAZ", array($token)));
                    }
                } else {
                    echo G::LoadTranslation('ID_LANGUAGE_CANT_DELETE_CURRENTLY');
                }
            } else {
                echo G::LoadTranslation('ID_LANGUAGE_CANT_DELETE_DEFAULT');
            }
            break;
    }
} catch (Exception $oException) {
    $token = strtotime("now");
    PMException::registerErrorLog($oException, $token);
    G::outRes(G::LoadTranslation("ID_EXCEPTION_LOG_INTERFAZ", array($token)));
}

