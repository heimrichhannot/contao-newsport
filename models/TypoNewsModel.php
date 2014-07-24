<?php

namespace HeimrichHannot\Typort;

class TypoNewsModel extends TypoModel
{

    protected static $strTable = 'tt_news';

    public static function findByPids($arrPids, $intStart=null, $intEnd=null, array $arrOptions=array())
    {
        if (!is_array($arrPids) || empty($arrPids))
        {
            return null;
        }

        $t = static::$strTable;
        $arrColumns = array("$t.pid IN(" . implode(',', array_map('intval', $arrPids)) . ")");

        $intStart = is_null($intStart) ? "NULL" : $intStart;
        $intEnd = is_null($intEnd) ? "NULL" : $intEnd;

        $arrColumns[] = "($intStart IS NULL OR $t.datetime>=$intStart) AND ($intEnd IS NULL OR $t.datetime<=$intEnd)";

        if (!isset($arrOptions['order']))
        {
            $arrOptions['order']  = "$t.datetime DESC";
        }

        return static::findBy($arrColumns, null, $arrOptions);
    }


}