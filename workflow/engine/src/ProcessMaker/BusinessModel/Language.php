<?php

namespace ProcessMaker\BusinessModel;

use Translation;

/**
 * Translation class
 *
 */
class Language
{

    /**
     * Web Entry 2.0 Rest - Get languages
     *
     * @category HOR-3209,PROD-181
     * @return array
     */
    public function getLanguageList()
    {
        $Translations = new Translation();
        $translationsTable = $Translations->getTranslationEnvironments();

        $availableLangArray = [];

        foreach ($translationsTable as $locale) {
            $row = [];
            $row['LANG_ID'] = $locale['LOCALE'];

            if ($locale['COUNTRY'] != '.') {
                $row['LANG_NAME'] = $locale['LANGUAGE'].' ('.
                    (ucwords(strtolower($locale['COUNTRY']))).')';
            } else {
                $row['LANG_NAME'] = $locale['LANGUAGE'];
            }

            $availableLangArray [] = $row;
        }

        return $availableLangArray;
    }
}
