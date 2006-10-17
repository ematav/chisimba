<?php

/* ------------icon request template----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

$objH = &$this->newObject('htmlheading','htmlelements');
$objH->type = 1;
$objH->str = $heading;

$tableHd[] = $this->objLanguage->languageText('mod_prelogin_blockinfo','prelogin');
$tableHd[] = ' ';

$table = &$this->newObject('htmltable','htmlelements');
$table->cellspacing = "2";
$table->cellpadding = "2";
$table->width = "50%";
$table->attributes = "border='0'";
$table->addHeader($tableHd,'heading','align="left"');

$this->loadClass('radio','htmlelements');
$this->loadClass('textarea','htmlelements');
$this->loadClass('textinput','htmlelements');

if (!isset($blockName)) {
	$blockName = '';
	$location = 'left';
	$blockContent = '';
}
$nameInput = &new textinput('title',$blockName,null,40);
$radio = &new radio('side');
$radio->addOption('left',$objLanguage->languageText('word_left'));
$radio->addOption('middle',$objLanguage->languageText('word_middle'));
$radio->addOption('right',$objLanguage->languageText('word_right'));
$radio->setSelected($location);
$contInput = &new textarea('content',htmlentities(html_entity_decode($blockContent,ENT_QUOTES),ENT_NOQUOTES),6,37);

$objModuleBlocks = &$this->getObject('dbmoduleblocks','modulecatalogue');
$blockList = $objModuleBlocks->getBlocks();
$moduleDrop = &new dropdown('moduleblock');
$moduleDrop->addOption(NULL,$this->objLanguage->languageText('mod_prelogin_selectblock','prelogin'));
if (isset($blockList)) {
	foreach($blockList as $moduleBlock){
	    $moduleDrop->addOption($moduleBlock['moduleid']."|".$moduleBlock['blockname'],$moduleBlock['moduleid']." - ".$moduleBlock['blockname']);
	}
}
if (isset($block)) {
	$moduleDrop->setSelected("{$block['module']}|{$block['name']}");
}

$submit = &new button('editform_submit',$this->objLanguage->languageText('word_update'));
$submit->setToSubmit();
$cancel = &new button('editform_cancel',$this->objLanguage->languageText('word_cancel'));
$returnUrl = $this->uri(array('action'=>'admin'));
$cancel->setOnClick("window.location = '$returnUrl'");

$table->startRow();
$table->addCell($this->objLanguage->languageText('mod_prelogin_blockname','prelogin'));
$table->addCell($nameInput->show());
$table->endRow();

$table->startRow();
$table->addCell($this->objLanguage->languageText('mod_prelogin_location','prelogin'));
$table->addCell($radio->show());
$table->endRow();

$table->startRow();
$table->addCell($this->objLanguage->languageText('word_content'));
$table->addCell($contInput->show());
$table->endRow();

$table->startRow();
$table->addCell($this->objLanguage->languageText('mod_prelogin_moduleblock','prelogin'));
$table->addCell($moduleDrop->show());
$table->endRow();

$table->startRow();
$table->addCell($submit->show().' '.$cancel->show());
$table->endRow();

$form = &new form('blockform',$this->uri(array('action'=>'submitblock')));
$form->addToForm($table);
if (isset($id)) {
	$form->addToForm(new textinput('id',$id,'hidden'));
}

$content = $objH->show().$form->show();
echo $content;

?>