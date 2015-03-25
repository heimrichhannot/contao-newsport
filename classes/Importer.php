<?php

namespace HeimrichHannot\Newsport;

use Contao\Model\Collection;

abstract class Importer extends \Backend
{
    protected $objModel;

    protected $objParentModel;

    protected $objItems;

    protected $arrData = array();

    protected $arrMapping = array();

    protected $arrNamedMapping = array();

    protected static $strTable;

    protected $Database;

    protected $arrDbSourceFields = array();

    protected $arrDbTargetFields = array();

    public function __construct($objModel)
    {
        if ($objModel instanceof \Model)
        {
            $this->objModel = $objModel;
        }
        elseif ($objModel instanceof \Model\Collection)
        {
            $this->objModel = $objModel->current();
        }

        parent::__construct();

        $this->arrData = $objModel->row();
        $this->objParentModel = NewsportModel::findByPk($this->objModel->pid);
        $this->Database = Database::getInstance($this->objParentModel->row());
        $this->arrDbSourceFields = $this->Database->listFields($this->dbTable);
        $this->arrDbTargetFields = \Database::getInstance()->listFields(static::$strTable);

        $this->arrMapping = $this->getFieldsMapping();

        $arrNamedMapping = $this->arrMapping;

        // name fields
        array_walk($arrNamedMapping, function(&$value, $index){
            $value = $value . ' as ' . $index;
        });

        $this->arrNamedMapping = $arrNamedMapping;
    }

    protected function getFieldMappingDbValue($arrSourceConfig, $arrTargetConfig)
    {
        $t = $this->dbTable;

        $strValue = $arrSourceConfig['name'];

        switch($arrSourceConfig['type'])
        {
            case 'timestamp':
                if($arrTargetConfig['type'] == 'int')
                {
                    $strValue = "UNIX_TIMESTAMP($t.$strValue)";
                }
            break;
            default:
                $strValue = $this->dbTable . '.' . $strValue;
        }

        return $strValue;
    }

    protected function getTargetDbConfig($strName)
    {
        foreach($this->arrDbTargetFields as $arrField)
        {
            if($strName == $arrField['name']) return $arrField;
        }

        return false;
    }

    protected function getSourceDbConfig($strName)
    {
        foreach($this->arrDbSourceFields as $arrField)
        {
            if($strName == $arrField['name']) return $arrField;
        }

        return false;
    }

    /**
     * run the importer
     * @return bool
     */
    public function run()
    {
        $this->collectItems();

        if($this->objItems === null)
        {
            return false;
        }

        $strClass = \Model::getClassFromTable(static::$strTable);

        if(!class_exists($strClass))
        {
            return false;
        }

        while($this->objItems->next())
        {
            $objItem = $this->createObjectFromMapping($this->objItems, $strClass);
            $this->createImportMessage($objItem);
        }

        return true;
    }

    protected function createObjectFromMapping($objSourceItem, $strClass)
    {
        $objItem = new $strClass();

        foreach($this->arrMapping as $key => $col)
        {
            $value = $objSourceItem->{$key};
            $arrCreateAfterSaving = array();
            $this->setObjectValueFromMapping($objItem, $value, $key, $arrCreateAfterSaving);
            $objItem->save();
        }

        // do after item has been created,
        $this->runAfterSaving($objItem, $objSourceItem);

        return $objItem;
    }

    protected function setObjectValueFromMapping(&$objItem, $value, $key)
    {
        // negate the value
        if(substr($key,0,1) == '!')
        {
            $key = preg_replace('/!/', '', $key, 1);
            $objItem->{$key} = !$value;
            return $objItem;
        }

        // fill multiple fields with one value
        $multipleKeys = trimsplit(',', $key);
        if(!empty($multipleKeys))
        {
            foreach($multipleKeys as $subKey)
            {
                $objItem->{$subKey} = $value;
            }
            return $objItem;
        }

        $objItem->$key = $value;
    }

    protected function collectItems()
    {
        $t = $this->dbTable;

        $intStart = intval($this->start ? $this->start : 0);
        $intEnd = intval($this->end ? $this->end : 2145913200);

        $strQuery = "SELECT " . implode(', ', $this->arrNamedMapping) . " FROM $t";

        $strDateCol = $this->arrMapping['date'];
        $strQuery .= " WHERE (($strDateCol>=$intStart AND $strDateCol<=$intEnd) OR ($strDateCol>=$intStart AND $strDateCol<=$intEnd) OR ($strDateCol<=$intStart AND $strDateCol>=$intEnd))";

        if($this->whereClause != '')
        {
            $strQuery .= " AND " . $this->whereClause;
        }

        ob_start();
        print_r($strQuery);
        print "\n";
        file_put_contents(TL_ROOT . '/debug.txt', ob_get_contents(), FILE_APPEND);
        ob_end_clean();

        $objResult = $this->Database->prepare($strQuery)->execute();

        $this->objItems = $objResult;
    }

    /**
     * Set an object property
     * @param string
     * @param mixed
     */
    public function __set($strKey, $varValue)
    {
        $this->arrData[$strKey] = $varValue;
    }


    /**
     * Return an object property
     * @param string
     * @return mixed
     */
    public function __get($strKey)
    {
        if (isset($this->arrData[$strKey]))
        {
            return $this->arrData[$strKey];
        }

        return parent::__get($strKey);
    }


    /**
     * Check whether a property is set
     * @param string
     * @return boolean
     */
    public function __isset($strKey)
    {
        return isset($this->arrData[$strKey]);
    }


    /**
     * Return the model
     * @return \Model
     */
    public function getModel()
    {
        return $this->objModel;
    }


    /**
     * @return Associated Array
     * Key = Typo 3 Field Name
     * Value = Contao Field Name
     */
    abstract protected function getFieldsMapping();


    abstract protected function runAfterSaving(&$objItem, $objTypoItem);

    protected function createImportMessage($objItem){}
}