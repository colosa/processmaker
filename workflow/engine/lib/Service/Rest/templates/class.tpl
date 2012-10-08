<?php

class Services_Rest_{{ classname }}
{
    /**
     * Structure of table '{{ tablename }}'
     *
     {% for columnType in type %}*  {{columnType}}
     {% endfor %}*
     */

{{ methods | safe }}
}
