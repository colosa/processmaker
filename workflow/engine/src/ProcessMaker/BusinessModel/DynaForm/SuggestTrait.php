<?php

namespace ProcessMaker\BusinessModel\DynaForm;

/**
 * SuggestTrait
 * Methods required to prepare the query for the suggest fields.
 */
trait SuggestTrait
{
    /**
     * Get field current value.
     *
     * @param type $json
     * @return type
     */
    private function getSuggestValue($json)
    {
        $value = "";
        if ($json->defaultValue !== "") {
            $value = $json->defaultValue;
        }
        if (isset($this->fields["APP_DATA"][$json->name])) {
            $value = $this->fields["APP_DATA"][$json->name];
        }
        return $value;
    }

    /**
     * Prepare the query for the suggest field.
     *
     * @param type $sql
     * @param type $json
     * @return type
     */
    private function prepareSuggestSql($sql, $json)
    {
        //If 0 find an specific row by the first column.
        $optionsLimit = empty($json->queryLimit) ? 0 : $json->queryLimit;
        return $this->sqlParse(
            $sql,
            function ($parsed, &$select, &$from, &$where, &$groupBy, &$having, &$orderBy, &$limit) use ($json, $optionsLimit)
            {
                $dt = $parsed["SELECT"];

                $isWhere = empty($where);
                if ($isWhere === false) {
                    $where = substr_replace($where, " (", 5, 0) . ")";
                }
                if (!isset($json->queryField) && isset($dt[0]["base_expr"])) {
                    $col = $dt[0]["base_expr"];
                    $dv = str_replace("'", "''", $json->defaultValue);
                    if ($dv !== "") {
                        $where = $isWhere ? "WHERE " . $col . "='" . $dv . "'" : $where . " AND " . $col . "='" . $dv . "'";
                    }
                }
                if (isset($json->queryField) && isset($dt[0]["base_expr"])) {
                    $col = isset($dt[1]["base_expr"]) ? $dt[1]["base_expr"] : $dt[0]["base_expr"];
                    $qf = str_replace("'", "''", $json->queryFilter);
                    $where = $isWhere ? "WHERE " . $col . " LIKE '%" . $qf . "%'" : $where . " AND " . $col . " LIKE '%" . $qf . "%'";
                }

                if ($optionsLimit > 0) {
                    $this->addSuggestLimit($json, $select, $limit, $where);
                } else {
                    $this->addSuggestWhere($json, $parsed, $select, $where, $having);
                }
            }
        );
    }

    /**
     * Add the limit sentence to the suggest query.
     *
     * @param type $json
     * @param type $select
     * @param type $limit
     * @param type $where
     */
    private function addSuggestLimit($json, &$select, &$limit, &$where)
    {
        $start = 0;
        $end = 10;
        $provider = $this->getDatabaseProvider($json->dbConnection);
        if (isset($json->queryStart)) {
            $start = $json->queryStart;
        }
        if (isset($json->queryLimit)) {
            $end = $json->queryLimit;
        }
        if (empty($limit) && $provider === "mysql") {
            $limit = "LIMIT " . $start . "," . $end . "";
        }
        if (empty($limit) && $provider === "pgsql") {
            $limit = "OFFSET " . $start . " LIMIT " . $end . "";
        }
        if ($provider === "mssql") {
            $limit = "";
            if (strpos(strtoupper($select), "TOP") === false) {
                $isDistinct = strpos(strtoupper($select), "DISTINCT");
                $isAll = strpos(strtoupper($select), "ALL");
                if ($isDistinct === false && $isAll === false) {
                    $select = preg_replace("/SELECT/",
                    "SELECT TOP(" . $end . ")",
                    strtoupper($select), 1);
                }
                if ($isDistinct !== false) {
                    $select = preg_replace("/DISTINCT/",
                    "DISTINCT TOP(" . $end . ")",
                    strtoupper($select), 1);
                }
                if ($isAll !== false) {
                    $select = preg_replace("/DISTINCT/",
                    "DISTINCT TOP(" . $end . ")",
                    strtoupper($select), 1);
                }
            }
        }
        if ($provider === "oracle") {
            $limit = "";
            $rowNumber = "";
            if (strpos(strtoupper($where), "ROWNUM") === false) {
                $rowNumber = " AND " . $start . " <= ROWNUM AND ROWNUM <= " . $end;
            }
            $where = empty($where)
                ? "WHERE " . $start . " <= ROWNUM AND ROWNUM <= " . $end
                : $where . $rowNumber;
        }
    }

    /**
     * If possible, add a where to get the current label.
     *
     * @param type $json
     * @param type $parsed
     * @param type $select
     * @param type $where
     * @param type $having
     * @return boolean
     */
    private function addSuggestWhere($json, $parsed, &$select, &$where, &$having)
    {
        $value = $this->escapeQuote($this->getSuggestValue($json));
        switch ($parsed['SELECT'][0]['expr_type']) {
            case 'colref':
            case 'expression':
                if (
                    is_array($parsed['SELECT'][0]['sub_tree'])
                    && $parsed['SELECT'][0]['sub_tree'][0]['expr_type']==='aggregate_function'
                ) {
                    return false;
                } else {
                    $where = empty($where)
                        ? "WHERE (" . $parsed['SELECT'][0]['base_expr'] . "='$value')"
                        : "$where and (" . $parsed['SELECT'][0]['base_expr'] . "='$value')";
                }
                return true;
        }
        return false;
    }

    /**
     * Escape a sql string.
     *
     * @param type $text
     * @return type
     */
    private function escapeQuote($text)
    {
        return str_replace("'", "''", $text);
    }

}
