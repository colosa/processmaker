 <?php
try {

    $CalendarUid = $_GET['id'];

    $calendarObj = new Calendar();
    $calendarObj->deleteCalendar( $CalendarUid );

    G::Header( 'location: calendarList' );

} catch (Exception $e) {
    $G_PUBLISH = new Publisher();
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish', 'blank' );
}

