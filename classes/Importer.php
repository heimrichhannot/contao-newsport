<?php

namespace HeimrichHannot\Typort;

abstract class Importer extends \Backend
{
    protected $objModel;

    protected $objItems;

    protected $arrData = array();

    protected $arrMapping = array();

    protected static $strTypoTable;

    protected static $strTable;

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
        $this->arrMapping = $this->getFieldsMapping();
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

    protected function createObjectFromMapping($objTypoItem, $strClass)
    {
        $objItem = new $strClass();

        foreach($this->arrMapping as $typo => $key)
        {
            $value = $objTypoItem->{$typo};
            $arrCreateAfterSaving = array();
            $this->setObjectValueFromMapping($objItem, $value, $key, $arrCreateAfterSaving);
            $objItem->save();
        }

        // do after item has been created,
        $this->runAfterSaving($objItem, $objTypoItem);

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
        $strClass = $GLOBALS['TL_MODELS'][static::$strTypoTable];

        if (!class_exists($strClass))
        {
            return false;
        }

        $this->objItems = $strClass::findByPids(array($this->pid), $this->start, $this->end);

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