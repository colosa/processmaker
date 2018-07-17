<?php

/**
 * PopupMenu - PopupMenu
/**
 * PopupMenu - PopupMenu class
 *
 * @package workflow.engine.ProcessMaker
 * @copyright COLOSA
 */
class PopupMenu extends Form
{
    var $type = 'PopupMenu';
    var $theme = 'processmaker';

    /**
     * Print the popup
     *
     * @param string $tableId
     * @param array $tableFields
     * @return array
     */
    function renderPopup ($tableId, $tableFields)
    {
        $this->name = $tableId;
        $fields = array_keys( $tableFields );
        foreach ($fields as $f) {
            switch (strtolower( $tableFields[$f]['Type'] )) {
                case 'javascript':
                case 'button':
                case 'private':
                case 'hidden':
                case 'cellmark':
                    break;
                default:
                    $label = ($tableFields[$f]['Label'] != '') ? $tableFields[$f]['Label'] : $f;
                    $label = str_replace( "\n", ' ', $label );
                    $pmXmlNode = new Xml_Node( $f, 'complete', '', array ('label' => $label,'type' => 'popupOption','launch' => $tableId . '.showHideField("' . $f . '")'
                    ) );
                    $this->fields[$f] = new XmlFormFieldPopupOption( $pmXmlNode );
                    $this->values[$f] = '';
            }
        }
        $scTemp = '';
        $this->values['PAGED_TABLE_ID'] = $tableId;
        print (parent::render( PATH_CORE . 'templates/popupMenu.html', $scTemp )) ;
        $sc = "<script type=\"text/javascript\">\n$scTemp\n loadPopupMenu_$tableId(); \n</script>";
        return $sc;
    }
}
