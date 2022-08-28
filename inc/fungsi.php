<?php

function get_string_between($string, $start, $end)
{
	$string = ' ' . $string;
	$ini = strpos($string, $start);
	if ($ini == 0) return '';
	$ini += strlen($start);
	$len = strpos($string, $end, $ini) - $ini;
	return substr($string, $ini, $len);
}


function rem_tag($tag)
{
	$str_st = '<td width=125';
	$str_en = 'blank">';

	$str_mid = get_string_between($tag, $str_st, $str_en);

	$str_all = $str_st . $str_mid . $str_en;

	$data = str_replace($str_all, '', $tag);

	return $data;
}


//original function by @TommyBudiawan

# Fungsi format NPWP dari '999999999999999' menjadi '99.999.999.9-999.999'
function format_npwp($npwp)
{
	$ret = substr($npwp, 0, 2) . "."
		. substr($npwp, 2, 3) . "."
		. substr($npwp, 5, 3) . "."
		. substr($npwp, 8, 1) . "-"
		. substr($npwp, 9, 3) . "."
		. substr($npwp, 12, 3);
	return $ret;
}

# Fungsi Trim NPWP menjadi '999999999999999' [15 digit]
function trim_npwp($npwp)
{
	$ret = preg_replace("/[^0-9]/", "", $npwp);
	return $ret;
}

function linkExtractor($html)
{
 $linkArray = array();
 if(preg_match_all('/<a\s+.*?href= [\"\']?([^\"\' >]*)[\"\']?[^>]*>(.*?)<\/a>/i', $html, $matches, PREG_SET_ORDER)){
  foreach ($matches as $match) {
   array_push($linkArray, array($match[1], $match[2]));
  }
 }
 return $linkArray;
}

function http_request($link){
    // persiapkan curl
    $ch = curl_init(); 

    // set url 
    curl_setopt($ch, CURLOPT_URL, $link);
    
    // set user agent    
    curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

    // return the transfer as a string 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

    // $output contains the output string 
    $output = curl_exec($ch); 

    // tutup curl 
    curl_close($ch);      

    // mengembalikan hasil curl	
    return $output;
}