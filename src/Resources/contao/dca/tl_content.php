<?php

/**
 * Paletten
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'][] = 'volunteeringlist_alttemplate';
$GLOBALS['TL_DCA']['tl_content']['palettes']['volunteeringlist'] = '{type_legend},type,headline;{volunteer_legend},volunteeringlist,volunteeringlist_alttemplate;{protected_legend:hide},protected;{expert_legend:hide},guest,cssID,space;{invisible_legend:hide},invisible,start,stop';
$GLOBALS['TL_DCA']['tl_content']['subpalettes']['volunteeringlist_alttemplate'] = 'volunteeringlist_template';

/**
 * Felder
 */

// volunteeringlistliste anzeigen

$GLOBALS['TL_DCA']['tl_content']['fields']['volunteeringlist'] = array
(
	'label'                => &$GLOBALS['TL_LANG']['tl_content']['volunteeringlist'],
	'default'              => 'text',
	'exclude'              => true,
	'options_callback'     => array('tl_content_volunteeringlist', 'getVolunteeringlist'),
	'inputType'            => 'select',
	'eval'                 => array
	(
		'mandatory'      => false, 
		'multiple'       => false, 
		'chosen'         => true,
		'submitOnChange' => true,
		'tl_class'       => 'wizard'
	),
	'wizard'               => array
	(
		array('tl_content_volunteeringlist', 'editListe')
	),
	'sql'                  => "int(10) unsigned NOT NULL default '0'" 
);

$GLOBALS['TL_DCA']['tl_content']['fields']['volunteeringlist_alttemplate'] = array
(
	'label'         => &$GLOBALS['TL_LANG']['tl_content']['volunteeringlist_alttemplate'],
	'exclude'       => true,
	'inputType'     => 'checkbox',
	'eval'          => array('tl_class'=>'clr','isBoolean' => true,'submitOnChange'=>true),
	'sql'           => "char(1) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_content']['fields']['volunteeringlist_template'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['volunteeringlist_template'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options_callback'        => array('tl_content_volunteeringlist', 'getTemplates'),
	'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
	'sql'                     => "varchar(64) NOT NULL default ''"
);

/*****************************************
 * Klasse tl_content_volunteeringlist
 *****************************************/
 
class tl_content_volunteeringlist extends \Backend
{

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}

	/**
	 * Funktion editAdresse
	 * @param \DataContainer
	 * @return string
	 */
	public function editListe(DataContainer $dc)
	{
		return ($dc->value < 1) ? '' : ' <a href="contao/main.php?do=volunteeringlist&amp;table=tl_volunteeringlist_items&amp;id=' . $dc->value . '&amp;popup=1&amp;rt=' . REQUEST_TOKEN . '" title="' . sprintf(specialchars($GLOBALS['TL_LANG']['tl_content']['editalias'][1]), $dc->value) . '" style="padding-left:3px" onclick="Backend.openModalIframe({\'width\':765,\'title\':\'' . specialchars(str_replace("'", "\\'", sprintf($GLOBALS['TL_LANG']['tl_content']['editalias'][1], $dc->value))) . '\',\'url\':this.href});return false">' . Image::getHtml('alias.gif', $GLOBALS['TL_LANG']['tl_content']['editalias'][0], 'style="vertical-align:top"') . '</a>';
	} 
	
	public function getVolunteeringlist(DataContainer $dc)
	{
		$array = array();
		$objAdresse = $this->Database->prepare("SELECT * FROM tl_volunteeringlist ORDER BY title ASC")->execute();
		while($objAdresse->next())
		{
			$array[$objAdresse->id] = $objAdresse->title;
		}
		return $array;

	}

	public function getTemplates($dc)
	{
		return $this->getTemplateGroup('mod_volunteeringlist_', $dc->activeRecord->id);
	} 

}

?>
