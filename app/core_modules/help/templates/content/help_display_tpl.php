<?php
//<div style="padding-bottom: 60px;">

$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('link', 'htmlelements');

$heading = new htmlheading();
$heading->type = 1;
$heading->str = $helptitle;//.' '.$viewletHelp;


$content = $heading->show().$helptext;


if (count($moduleHelp) > 0) {
    $content .= '<h5>Related Help for this Module</h5><ul>';

    $link = new link();
    foreach ($moduleHelp as $text)
    {

        if ($text['code'] == 'help_'.$module.'_about_title') {
            $helpItem = 'about';
        } else {
            $helpItem = str_replace('help_'.$module.'_title_', '', $text['code']);
        }

        $link->href = $this->uri(array('action'=>'view', 'rootModule'=>$module, 'helpid'=>$helpItem));
        $helpTitle = $objLanguage->code2Txt($text['code'], $module);

        if (strtoupper(substr($helpTitle, 0, 12)) == '[*HELPLINK*]') {
            $array = explode('/', $helpTitle);
            $helpTitle = $objLanguage->code2Txt('help_'.$array[1].'_title_'.$array[2], $module);
        }

        $link->link = $helpTitle;
        $content .= '<li>'.$link->show().'</li>';
    }

    $content .= "</ul>";
}


echo $content;
//</div>

//<div style="position: fixed; height: 40px; bottom: 0; left: 0; width:100%; right: 0; padding: 5px;" id="footer"><?php //echo $richHelp; 
//</div>
?>