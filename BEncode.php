<?php

/*
TorrentParse GTK - Parse Bittorrent Torrent Files in a cross-compatible GTK GUI
    Copyright (C) 2004  KunKie

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

Originally Distributed with PHPBTTRACKER
*/
// Woohoo! Who needs mhash or PHP 4.3?
// Don't require it. Still recommended, but not mandatory.
if (!function_exists("sha1"))
	@include_once("sha1lib.php");


// We'll protect the namespace of our code
// using a class
class BEncode
{

// Dictionary keys must be sorted. foreach tends to iterate over the order
// the array was made, so we make a new one in sorted order. :)
function makeSorted($array)
{
	$i = 0;

	// Shouldn't happen!
	if (empty($array))
		return $array;

	foreach($array as $key => $value)
		$keys[$i++] = stripslashes($key);
	sort($keys);
	for ($i=0 ; isset($keys[$i]); $i++)
		$return[addslashes($keys[$i])] = $array[addslashes($keys[$i])];
	return $return;
}

// Encodes strings, integers and empty dictionaries.
// $unstrip is set to true when decoding dictionary keys
function encodeEntry($entry, &$fd, $unstrip = false)
{
	if (is_bool($entry))
	{
		$fd .= "de";
		return;
	}
	if (is_int($entry) || is_float($entry))
	{
		$fd .= "i".$entry."e";
		return;
	}
	if ($unstrip)
		$myentry = stripslashes($entry);
	else
		$myentry = $entry;
	$length = strlen($myentry);
	$fd .= $length.":".$myentry;
	return;
}

// Encodes lists
function encodeList($array, &$fd)
{
	$fd .= "l";

	// The empty list is defined as array();
	if (empty($array))
	{
		$fd .= "e";
		return;
	}
	for ($i = 0; isset($array[$i]); $i++)
		$this->decideEncode($array[$i], $fd);
	$fd .= "e";
}

// Passes lists and dictionaries accordingly, and has encodeEntry handle
// the strings and integers.
function decideEncode($unknown, &$fd)
{
	if (is_array($unknown))
	{
		if (isset($unknown[0]) || empty($unknown))
			return $this->encodeList($unknown, $fd);
		else
			return $this->encodeDict($unknown, $fd);
	}
	$this->encodeEntry($unknown, $fd);
}

// Encodes dictionaries
function encodeDict($array, &$fd)
{
	$fd .= "d";
	if (is_bool($array))
	{
		$fd .= "e";
		return;
	}
	// NEED TO SORT!
	$newarray = $this->makeSorted($array);
	foreach($newarray as $left => $right)
	{
		$this->encodeEntry($left, $fd, true);
		$this->decideEncode($right, $fd);
	}
	$fd .= "e";
	return;
}



} // End of class declaration.

// Use this function in your own code.
function BEncode($array)
{
	$string = "";
	$encoder = new BEncode;
	$encoder->decideEncode($array, $string);
	return $string;
}


?>