 <?php
try {
    $form = $_POST['form'];
    $CategoryUid = $form['CATEGORY_UID'];
    $CategoryParent = $form['CATEGORY_PARENT'];
    $CategoryName = $form['CATEGORY_NAME'];
    $CategoryIcon = $form['CATEGORY_ICON'];

    require_once ("classes/model/ProcessCategory.php");

    //if exists the row in the database propel will update it, otherwise will insert.
    $tr = ProcessCategoryPeer::retrieveByPK( $CategoryUid );
    $processCategory = new ProcessCategory();
    $aProcessCategory = $processCategory->loadByCategoryName( $CategoryName );
    if (! is_array( $aProcessCategory )) {

        if (! (is_object( $tr ) && get_class( $tr ) == 'ProcessCategory')) {
            $tr = new ProcessCategory();
        }
        $tr->setCategoryUid( $CategoryUid );
        $tr->setCategoryParent( $CategoryParent );
        $tr->setCategoryName( $CategoryName );
        $tr->setCategoryIcon( $CategoryIcon );

        if ($tr->validate()) {
            // we save it, since we get no validation errors, or do whatever else you like.
            $res = $tr->save();
        } else {
            // Something went wrong. We can now get the validationFailures and handle them.
            $msg = '';
            $validationFailuresArray = $tr->getValidationFailures();
            foreach ($validationFailuresArray as $objValidationFailure) {
                $msg .= $objValidationFailure->getMessage() . "<br/>";
            }
            //return array ( 'codError' => -100, 'rowsAffected' => 0, 'message' => $msg );
        }
        //return array ( 'codError' => 0, 'rowsAffected' => $res, 'message' => '');


        //to do: uniform  coderror structures for all classes


        //if ( $res['codError'] < 0 ) {
        //  G::SendMessageText ( $res['message'] , 'error' );
        //}
        G::Header( 'location: processCategoryList' );
    } else {
        // G::SendTemporalMessage("El registro ya existe", "warning", 'labels');
        G::Header( 'location: processCategoryList' );
        die();

    }
} catch (Exception $e) {
    $G_PUBLISH = new Publisher();
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish', 'blank' );
}
