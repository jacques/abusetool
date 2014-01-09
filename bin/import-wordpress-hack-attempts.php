<?php
/**
 * Process abuse tools IMAP inbox
 *
 * @author Jacques Marneweck <jacques@powertrip.co.za>
 * @copyright 2013-2014 Jacques Marneweck.  All rights reserved.
 */

require_once 'MDB2.php';

$config = json_decode(file_get_contents(__DIR__ . '/../etc/config.json'));

$dsn = 'mysqli://' . $config->db->username . ':' . $config->db->password . '@' . $config->db->hostname . '/abusetool';

$options = array(
    'debug'       => 2,
    'portability' => MDB2_PORTABILITY_ALL,
);

echo "[" . date(DateTime::ISO8601) . "] About to connect to the MySQL database server...\n";

$mdb2 =& MDB2::connect($dsn, $options);
if (PEAR::isError($mdb2)) {
    die($mdb2->getMessage());
}
$mdb2->setFetchMode(MDB2_FETCHMODE_ASSOC);
echo "[" . date(DateTime::ISO8601) . "] Connected to MySQL...\n";

echo "[" . date(DateTime::ISO8601) . "] Setting up a prepared statement...\n";
$sth = $mdb2->prepare('INSERT INTO wp_hack_attempts (site, username, ipaddress, attempted_at) VALUES (?, ?, ?, ?)', array('text', 'text', 'text', 'text'), MDB2_PREPARE_MANIP);

$mbox = imap_open("{" . config->mailboxes->wphacks->hostname . ":143/novalidate-cert}INBOX", $config->mailboxes->wphacks->username, $config->mailboxes->wphacks->password);
$list = imap_list($mbox, $config->mailboxes->wphacks->u . ":143/novalidate-cert", "*");

/**
 * Process messages in the inbox
 */
for ($i = 1; $i < imap_num_msg($mbox)+1; $i++) {
  $headers = imap_header($mbox, $i);

  $date = date("Y-m-d H:i:s", $headers->udate);
  $site = $headers->subject;
  $site = ltrim($site, "[");
  $site = str_replace("] Site Lockout Notification", "", $site);

  $body = imap_body($mbox, $i);
  preg_match("/Username: (\w+)/", $body, $matches);
  $username = $matches['1'];
  preg_match("/IP Address: (\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})/", $body, $matches);
  $ipaddress = $matches['1'];

  if(
    !empty($username) &&
    !empty($ipaddress)
  ) {
    $aff = $sth->execute(array($site, $username, $ipaddress, $date));
    assert(1 === $aff);
    /**
     * Mark the processed message as being deleted
     */
    imap_delete($mbox, $i);
  }
}

/**
 * Expunge all the deleted messages and close the imap connection
 */
imap_expunge($mbox);
imap_close($mbox);
