<?php

/** Module voor adodb lite queries te kunnen draaien met TinyButStrong
 *  Module geschreven door Kevin Vranken in 2010
 *  onder de GNU Lesser General Public License v2 licence - gnu.org
 *
 * @global <type> $db
 * @param <type> $source
 * @param <type> $query
 * @return <type>
 */

function tbsdb_pear_ADOConnection_open(&$source,&$query) {
	global $db ;
	$rs = $db->Execute($query) ;
	return $rs ;
}

function tbsdb_pear_ADOConnection_fetch(&$rs) {
        $temp = $rs->FetchRow();
        arrayNaarUTF($temp);
	return $temp;
}

function tbsdb_pear_ADOConnection_close(&$rs) {
	$rs->Close() ;
}


?>