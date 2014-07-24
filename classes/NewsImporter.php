<?php

namespace HeimrichHannot\Typort;


class NewsImporter extends Importer
{
    protected static $strTypoTable = 'tt_news';

    protected static $strTable = 'tl_news';

    protected function getFieldsMapping()
    {
        $arrMap = array
        (
            'tstamp'                    => 'tstamp',
            'hidden'                    => '!published',
            'datetime'                  => 'date,time',
            'title'                     => 'headline',
            'short'                     => 'teaser'
        );

        return $arrMap;
    }

    protected function runAfterSaving(&$objItem, $objTypoItem)
    {
        $objItem->pid = $this->newsArchive;
        $objItem->alias = $this->generateAlias($objItem->headline, $objItem);

        $this->createContentElements($objItem, $objTypoItem);
        $this->createEnclosures($objItem, $objTypoItem);
        $objItem->teaser = "<p>" . strip_tags($objItem->teaser) . "</p>";

        $objItem->save();
    }

    protected function createEnclosures(&$objItem, $objTypoItem)
    {
        if($this->folder === null)
        {
            return false;
        }

        $objRefs = TypoRefIndexModel::findByRecUidsAndTableAndField(array($objTypoItem->uid), static::$strTypoTable, 'news_files');

        if($objRefs === null)
        {
            return false;
        }

        $objFolder = \FilesModel::findByUuid($this->folder);

        if($objFolder === null)
        {
            return false;
        }

        $arrEnclosure = array();

        while($objRefs->next())
        {
            if(!file_exists(TYPO3_ROOT . '/' . $objRefs->ref_string))
            {
                continue;
            }

            $objFile = new \File($objFolder->path . '/' . basename($objRefs->ref_string));
            $objFile->write(file_get_contents(TYPO3_ROOT . '/' . $objRefs->ref_string));
            $objFile->close();
            $objModel = $objFile->getModel();
            $arrEnclosure[] = $objModel->uuid;
        }

        if(!empty($arrEnclosure))
        {
            $objItem->addEnclosure = true;
            $objItem->enclosure = $arrEnclosure;
        }
    }

    protected function createContentElements(&$objItem, $objTypoItem)
    {
        if($objTypoItem->bodytext)
        {
            // need to wrap <p> around text for contao
            $tidyConfig = array
            (
                'enclose-text' => true
            );

            $bodyText = '<!DOCTYPE html><head><title></title></head><body>' . $objTypoItem->bodytext . '</body></html>';

            $tidy = new \tidy();
            $tidy->parseString($bodyText, $tidyConfig, $GLOBALS['TL_CONFIG']['dbCharset']);
            $body = $tidy->body();

            $objContent = new \ContentModel();
            $objContent->text = trim(str_replace(array('<body>', '</body>'), '', $body));
            $objContent->ptable = static::$strTable;
            $objContent->pid = $objItem->id;
            $objContent->sorting = 16;
            $objContent->tstamp = time();
            $objContent->type = 'text';
            $objContent->save();
        }
    }

    public function generateAlias($varValue, $objItem)
    {
        $t = static::$strTable;

        $varValue = standardize(\String::restoreBasicEntities($varValue));

        $objAlias = \Database::getInstance()->prepare("SELECT id FROM $t WHERE alias=?")
            ->execute($varValue);

        // Add ID to alias
        if ($objAlias->numRows)
        {
            $varValue .= '-' . $objItem->id;
        }

        return $varValue;
    }

    protected function createImportMessage($objItem)
    {
        \Message::addConfirmation('Successfully imported news: "' . $objItem->headline . '"');
    }
}