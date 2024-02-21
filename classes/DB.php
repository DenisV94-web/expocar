<?php

class DB 
{
    private $db;

    public function __construct()
    {
        try 
        {
            $db_conn = json_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/test/settings/db_conn.json"));
            $this->db = new PDO($db_conn->dsn, $db_conn->username, $db_conn->password);
        } catch (PDOException $e) 
        {
            throw new Exception('DB construct error: ' . $e->getMessage());
        }
        
    }

    public function getList($table, $filter = array(), $select = array(), $order = array()) 
    {
        if ($table == "")
            throw new Exception('Required parameter "table" is missing');

        try 
        {
            $query = 'SELECT ';
            $fetchMode = PDO::FETCH_ASSOC;
            // if (count($select) == 1) 
            //     $fetchMode = PDO::FETCH_COLUMN;

            if (!count($select)) 
                $select = array("*");

            $query .= implode(", ", $select);

            $query .= " FROM {$table}";

            if (count($filter)) 
                $query .= " WHERE ".implode(" = ? AND ", array_keys($filter))." = ?";

            if (count($order)) 
                $query .= " ORDER BY ".str_replace("&", " AND ", http_build_query($order));

            $prepare = $this->db->prepare($query);
            if (count($filter)) {
                $prepare->execute(array_values($filter));
            } else {
                $prepare->execute();
            }

            

            return $prepare->fetchAll($fetchMode);
        } catch (PDOException $e) 
        {
            throw new Exception('DB getList error: ' . $e->getMessage());
        }
    }

    public function insert($table, $values) 
    {
        if ($table == "")
            throw new Exception('Required parameter "table" is missing');

        if (!count($values))
            throw new Exception('Required parameter "values" is missing');

        try 
        {
            $columnsName = "(".implode(", ", array_keys($values[0])).")";

            $columnsValue = "";
            foreach ($values as $key => $value) {
                $suffix = "";
                if ($key < count($values)-1) 
                    $suffix = ", ";

                $columnsValue .= "('" . implode("', '", array_values($value)) . "')" . $suffix;
            }

            $query = "INSERT {$table} {$columnsName} VALUES {$columnsValue}";

            $affectedRowsNumber = $this->db->exec($query);

            return $affectedRowsNumber;

        } catch (PDOException $e) 
        {
            throw new Exception('DB insert error: ' . $e->getMessage());
        }
    }

    public function convertString($arr) {
        $str = "";
        $i=0;
        foreach ($arr as $key => $val) 
        {
            $suffix = "";
            if ($i < count($arr)-1) $suffix = " AND ";

            $str .= "{$key} = '{$val}'{$suffix}";
            $i++;
        }

        return $str;
    }

    public function update($table, $filter = array(), $updates = array(), $order = array()) {
        if ($table == "")
            throw new Exception('Required parameter "table" is missing');

        if (!count($filter))
            throw new Exception('Required parameter "filter" is missing');

        if (!count($updates))
            throw new Exception('Required parameter "updates" is missing');

        try 
        {
            
            $where = $this->convertString($filter);
            $set = $this->convertString($updates);

            $query = "UPDATE {$table} SET {$set} WHERE {$where}";

            $affectedRowsNumber = $this->db->exec($query);

            return $affectedRowsNumber;
        } catch (PDOException $e) 
        {
            throw new Exception('DB update error: ' . $e->getMessage());
        }
    }
    
}