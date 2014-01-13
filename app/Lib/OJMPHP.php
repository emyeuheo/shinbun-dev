<?php

class OJMPHP {

    private $jsonFile;
    private $jsonArray;
    private $jsonContent;
    private $dirtyFields;

    public function OJMPHP($jsonFile = null, $jsonArray = null) {
        $this->jsonFile = $jsonFile;
        if (file_exists($this->jsonFile)) {
            $json_content = file_get_contents($this->jsonFile);
        }

        if (!empty($json_content)) {
            
            $json_array = json_decode($json_content, true);
            if (!is_array($json_array)) {
                $this->jsonContent = json_last_error();
            } else {
                $this->jsonArray = $json_array;
                $this->jsonContent = $json_content;
            }
        }

        if (!empty($jsonArray)) {
            if (!empty($this->jsonArray) && is_array($this->jsonArray)) {
                $this->jsonArray = array_merge($this->jsonArray, $jsonArray);
            } else {
                $this->jsonArray = $jsonArray;
            }
        }
    }

    public function isFileExists() {
        return file_exists($this->jsonFile);
    }

    public function getJsonFileName() {
        return $this->jsonFile;
    }

    public function delete() {
        if (empty($this->jsonFile)) {
            return;
        }
        $fp = fopen($this->jsonFile, "w+");
        fwrite($fp, "");
        fclose($fp);
        $this->jsonArray = array();
        $this->dirtyFields = array();
        $this->jsonContent = "";
    }

    public function save($pretty_print = false) {
        if (empty($this->jsonFile)) {
            return;
        }
        if ($pretty_print  &&  (version_compare(phpversion(), '5.4', '>='))) {//support only >= 5.4
            $json_content = json_encode($this->jsonArray, JSON_PRETTY_PRINT);
        } else {
            $json_content = json_encode($this->jsonArray);
        }
        $fp = fopen($this->jsonFile, "w+");
        if (!empty($json_content)) {
            fwrite($fp, $json_content);
            $this->dirtyFields = array();
            $this->jsonContent = $json_content;
        }
        fclose($fp);
    }

    public function asArray() {
        return $this->jsonArray;
    }

    public function jsonText() {
        return $this->jsonContent;
    }

    /**
     * Check whether the given field has been changed since this
     * object was saved.
     */
    public function isDirty($key) {
        return isset($this->dirtyFields[$key]);
    }

    /**
     * Return the value of a property of this object
     * or null if not present.
     */
    public function get($key) {
        if (isset($this->jsonArray[$key])) {
            return trim($this->jsonArray[$key]);
        }
        return null;
    }

    /**
     * Set a property to a particular value on this object.
     * Flags that property as 'dirty' so it will be saved to the
     * database when save() is called.
     */
    public function set($key, $value) {
        $this->jsonArray[$key] = $value;
        $this->dirtyFields[$key] = $value;
    }

    // --------------------- //
    // --- MAGIC METHODS --- //
    // --------------------- //
    public function __get($key) {
        return $this->get($key);
    }

    public function __set($key, $value) {
        $this->set($key, $value);
    }

    public function __isset($key) {
        return isset($this->jsonArray[$key]);
    }

}

// --------------------- //
// --- UNIT TEST --- //
// --------------------- //
/*
$jsonClick = new OJMPHP('../tmp/ut_ojm_php.dat', 
								array('click' => 0, 
									'personal' => array('nickname' => 'anhnn',
														'age' => 32,
												)
								)
							);
$num_of_clicks = $jsonClick->click;

$num_of_clicks++;
$jsonClick->click = $num_of_clicks;

$jsonClick->save();

var_export($jsonClick->jsonText());
var_export($jsonClick->asArray());
*/