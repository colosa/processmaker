    /**
     * Implementation for 'POST' method for Rest API
     *
     {% for pk in primaryKeys %}* @param  mixed {{ pk }} Primary key
     {% endfor %}*
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function post({{ paramsStr }})
    {
        try {
            $result = array();
            $obj = new {{ classname }}();

            {% for param in params %}$obj->set{{ param | Capfirst }}(${{ param }});
            {% endfor %}
            $obj->save();
        } catch (Exception $e) {
            throw new RestException(412, $e->getMessage());
        }
    }
