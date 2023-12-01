<?php

require_once 'init.php';

new TSession;

$content = file_get_contents("install.html");

$content = BuilderTemplateParser::parse($content);

ob_start();
//$form = new DatabaseInstall();
$form = new ExtensionsInstall();
//$form->setIsWrapped(true);
$form->show();
$formContent = $mainContent = ob_get_contents();
ob_end_clean();

$content  = str_replace('{$content}', $formContent, $content);

echo $content;