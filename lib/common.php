<?php
/**
 * Abuse Tool Common functions
 *
 * @author Jacques Marneweck <jacques@powertrip.co.za>
 * @copyright 2013-2014 Jacques Marneweck.  All rights reserved.
 */

function load_config()
{
  $GLOBALS['config'] = json_decode(file_get_contents(__DIR__ . '/../etc/config.json'));
}
