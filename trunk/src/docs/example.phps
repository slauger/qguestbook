<?php
/**
 * Code Auschnitt aus qCMS
 * Diese neue Fehlersystem wird nun auch hier benutzt
 * @package qCMS
 */

/**
 * @ignore
 */

$lang['sql_error'] = "SQL Fehler %s";
$lang['sql_error_explain'] = "Es ist ein Fehler aufgetreten, bitte kontaktiere den Administrator<br /><br />%1s<br /><br />%2s<br /><br />%3s, Zeile %4s";

$qDatabase->load_extension('build_sql');

if (!$qDatabase->sql_insert(CONFIG_TABLE, array('config_name', 'config_value'), array($db->sql_escape('foo'), $db->sql_escape('foobar!'))))
{
	$error = $qDatabase->sql_error();
	message_die(sprintf($lang['sql_error'], $error['code']), sprintf($lang['sql_error_explain'], $error['error'], $error['sql'], __FILE__, __LINE__));
}

$qConfig->RefreshConfig();
$qConfig->GetConfValue('foo'); // returns "foobar!"

?>
