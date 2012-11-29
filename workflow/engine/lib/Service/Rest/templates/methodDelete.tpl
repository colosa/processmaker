    /**
     * Implementation for 'DELETE' method for Rest API
     *
     {% for pk in primaryKeys %}* @param  mixed {{ pk }} Primary key
     {% endfor %}*
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function delete({{ primaryKeys }})
    {
        $conn = Propel::getConnection({{ classname }}Peer::DATABASE_NAME);
        
        try {
            $conn->begin();
        
            $obj = {{ classname }}Peer::retrieveByPK({{ primaryKeys }});
            if (! is_object($obj)) {
                throw new RestException(412, 'Record does not exist.');
            }
            $obj->delete();
        
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
            throw new RestException(412, $e->getMessage());
        }
    }
