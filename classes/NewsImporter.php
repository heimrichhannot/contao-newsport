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
			'tstamp'   => 'tstamp',
			'hidden'   => '!published',
			'datetime' => 'date,time',
			'title'    => 'headline',
			'short'    => 'teaser'
		);

		return $arrMap;
	}

	protected function runAfterSaving(&$objItem, $objTypoItem)
	{
		$objItem->pid      = $this->newsArchive;
		$objItem->alias    = $this->generateAlias($objItem->headline, $objItem);
		$objItem->source   = 'default';
		$objItem->floating = 'above';

		$this->createContentElements($objItem, $objTypoItem);
		$this->createEnclosures($objItem, $objTypoItem);

		// news_categories module support
		if (in_array('news_categories', \Config::getInstance()->getActiveModules())) {
			$this->setCategories($objItem, $objTypoItem);
		}

		$objItem->teaser = "<p>" . strip_tags($objItem->teaser) . "</p>";

		$objItem->save();
	}

	protected function setCategories(&$objItem, $objTypoItem)
	{
		$arrCatTypo   = deserialize($this->catTypo);
		$arrCatContao = deserialize($this->catContao);

		if (empty($arrCatContao) || empty($arrCatTypo)) return false;

		$arrCatTypoIds   = array_values($arrCatTypo);
		$arrCatContaoIds = array_values($arrCatContao);

		$objItemCategories = Database::getInstance()->prepare('SELECT * FROM tt_news_cat_mm WHERE uid_local = ? ORDER BY sorting')->execute($objTypoItem->uid);

		if ($objItemCategories->count() < 1) return false;

		$arrCategories = array();

		while ($objItemCategories->next()) {
			$idxTypo = array_search($objItemCategories->uid_foreign, $arrCatTypoIds);

			if ($idxTypo === false || !isset($arrCatContao[$idxTypo])) continue;

			$idxContao = $arrCatContaoIds[$idxTypo]; // set id by mapping

			$arrCategories[] = $idxContao;
		}

		$objItem->categories = serialize($arrCategories);

		return true;
	}

	protected function createEnclosures(&$objItem, $objTypoItem)
	{
		if ($this->folder === null) {
			return false;
		}

		$objRefs = TypoRefIndexModel::findByRecUidsAndTableAndField(array($objTypoItem->uid), static::$strTypoTable, 'news_files');

		if ($objRefs === null) {
			return false;
		}

		$objFolder = \FilesModel::findByUuid($this->folder);

		if ($objFolder === null) {
			return false;
		}

		$arrEnclosure = array();

		while ($objRefs->next()) {
			if (!file_exists(TYPO3_ROOT . '/' . $objRefs->ref_string)) {
				continue;
			}

			$objFile = new \File($objFolder->path . '/' . basename($objRefs->ref_string));
			$objFile->write(file_get_contents(TYPO3_ROOT . '/' . $objRefs->ref_string));
			$objFile->close();
			$objModel       = $objFile->getModel();
			$arrEnclosure[] = $objModel->uuid;
		}

		if (!empty($arrEnclosure)) {
			$objItem->addEnclosure = true;
			$objItem->enclosure    = $arrEnclosure;
		}
	}

	protected function createContentElements(&$objItem, $objTypoItem)
	{
		if ($objTypoItem->bodytext) {
			// need to wrap <p> around text for contao
			$tidyConfig = array
			(
				'enclose-text'                => true,
				'drop-font-tags'              => true,
				'drop-proprietary-attributes' => true,
				'quote-ampersand'             => true,
			);

			$bodyText = '<!DOCTYPE html><head><title></title></head><body>' . $objTypoItem->bodytext . '</body></html>';

			$bodyText = $this->convert_external_link_tags($bodyText);
			$bodyText = $this->convert_internal_link_tags($bodyText);
			$bodyText = $this->nl2p($bodyText);

			$tidy = new \tidy();
			$tidy->parseString($bodyText, $tidyConfig, $GLOBALS['TL_CONFIG']['dbCharset']);
			$body = $tidy->body();

			$objContent       = new \ContentModel();
			$objContent->text = trim(str_replace(array('<body>', '</body>'), '', $body));
			$objContent->text = preg_replace("/<img[^>]+\>/i", "", $objContent->text); // strip images
			// create links from text
			$objContent->text = preg_replace('!(\s|^)((https?://|www\.)+[a-z0-9_./?=&-]+)!i', ' <a href="http://$2" target="_blank">$2</a>', $objContent->text);
			// replace <b> by <strong>
			$objContent->text = preg_replace('!<b(.*?)>(.*?)</b>!i', '<strong>$2</strong>', $objContent->text);
			// replace emails with inserttags
			$objContent->text = preg_replace('/([A-Z0-9._%+-]+)@([A-Z0-9.-]+)\.([A-Z]{2,4})(\((.+?)\))?/i', "{{email::$1@$2.$3}}", $objContent->text);

			ob_start();
			print_r($objContent->text);
			print "\n";
			file_put_contents(TL_ROOT . '/debug.txt', ob_get_contents(), FILE_APPEND);
			ob_end_clean();

			$objContent->ptable  = static::$strTable;
			$objContent->pid     = $objItem->id;
			$objContent->sorting = 16;
			$objContent->tstamp  = time();
			$objContent->type    = 'text';
			$objContent->save();
		}
	}

	public function nl2p($string)
	{
		$string = preg_replace('#<br\s*/?>#i', "\n", $string); // replace br with new line

		$paragraphs = '';

		foreach (explode("\n", $string) as $line) {
			if (trim($line)) {
				$paragraphs .= '<p>' . $line . '</p>';
			}
		}

		return $paragraphs;
	}


	public function convert_external_link_tags($html)
	{
		$pattern     = '!<link\s(.+)\s_blank external-link-new-window\s(".*")?><img(.+) \/>(.+)<\/link>!U';
		$replacement = '<a href="$1" target="_blank" title=$2>$4</a>';
		preg_match_all($pattern, $html, $matches, PREG_PATTERN_ORDER);

		return preg_replace($pattern, $replacement, $html);
	}

	public function convert_internal_link_tags($html)
	{
		$pattern     = '!<link\s(\d+)\s-\sinternal-link\s(".*")?>((https?://|www\.)+[a-z0-9_./?=&-]+)<\/link>!U';
		$replacement = '<a href="http://$3" title=$2>$3</a>';
		preg_match_all($pattern, $html, $matches, PREG_PATTERN_ORDER);

		return preg_replace($pattern, $replacement, $html);
	}

	public function generateAlias($varValue, $objItem)
	{
		$t = static::$strTable;

		$varValue = standardize(\String::restoreBasicEntities($varValue));

		$objAlias = \Database::getInstance()->prepare("SELECT id FROM $t WHERE alias=?")
			->execute($varValue);

		// Add ID to alias
		if ($objAlias->numRows) {
			$varValue .= '-' . $objItem->id;
		}

		return $varValue;
	}

	protected function createImportMessage($objItem)
	{
		\Message::addConfirmation('Successfully imported news: "' . $objItem->headline . '"');
	}
}