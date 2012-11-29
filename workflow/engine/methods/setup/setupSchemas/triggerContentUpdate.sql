CREATE TRIGGER CONTENT_UPDATE BEFORE UPDATE ON CONTENT

FOR EACH ROW
BEGIN

  DECLARE str TEXT;
  
  IF (NEW.CON_VALUE IS NULL) THEN
    SET str = '';
  ELSE
    SET str = NEW.CON_VALUE;
  END IF;
  
  CASE (NEW.CON_CATEGORY)
    WHEN 'APP_TITLE' THEN
      BEGIN
        UPDATE APP_CACHE_VIEW
        SET    APP_TITLE = str
        WHERE  APP_UID = NEW.CON_ID;
      END;
    
    WHEN 'PRO_TITLE' THEN
      BEGIN
        UPDATE APP_CACHE_VIEW
        SET    APP_PRO_TITLE = str
        WHERE  PRO_UID = NEW.CON_ID;
      END;
      
    WHEN 'TAS_TITLE' THEN
      BEGIN
        UPDATE APP_CACHE_VIEW
        SET    APP_TAS_TITLE = str
        WHERE  TAS_UID = NEW.CON_ID;
      END;
    
    ELSE
      BEGIN
      END;
  END CASE;
  
END;