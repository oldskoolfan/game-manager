<?php
/**
 * Get our mysql connection. In the real world we'd store this
 * file outside of htdocs.
 *
 * mysqli constructor params are host, username, password, database
 */
$con = new mysqli('localhost','root','','gamedb');
