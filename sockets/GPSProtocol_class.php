<?php
/*
 * Copyright © 2012 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: GPSProtocol_class.php
 * Created:        @alain.paulikevitch    Dec 19, 2012 | 1:05:29 PM
 * Last Update:    @alain.paulikevitch    Dec 19, 2012 | 1:05:29 PM
 */


$fh = fopen('socketdata2.txt', 'r');
while (!feof($fh)) {
    $line = fgets($fh);
	much_one_location($line);
}
fclose($fh);



function getsome($howmany,$string=null) {
	static $data;
	if (!isset($data)) {
		if (isset($string)) {
			$data=$string;
		} else {
			return null;
		}
	} else {
		if (isset($string)) {
			$data=$string;
		}
	}
	$return=substr($data, 0, $howmany);
	$data=substr($data, $howmany, strlen($data)-$howmany);
	return $return;
}

function much_one_location($line) {
	echo '<pre>'.bin2hex($line).'</pre>';
	$label='';
	$hexdat='';
	$value='';
	for ($i=0;$i<strlen($line);$i++) {
	   if ($i==0) {
		   if (bin2hex($line[$i])!='29')
			   break;
		   $hexdat.='<font color=green>';
	   }
	   if ($i==2)
		   $hexdat.='<font color=red><b>';
	   if ($i==3)
	   {
			$value.=str_pad(hexdec(bin2hex($line[$i]).bin2hex($line[$i+1])),5,'0',STR_PAD_LEFT);
			$hexdat.='<font color=blue>';
	   } else {
			$value.='   ';
	   }
   	   if ($i==5)
	   {
			$hexdat.='<font color=purple>';
	   }

	   $label.=str_pad($i,2,'0',STR_PAD_LEFT).'<font color=gray style="font-weight:normal;">|</font>';
	   $hexdat.=bin2hex($line[$i]).'<font color=black style="font-weight:normal;">|</font>';
	   if ($i==1)
		   $hexdat.='</font>';
   	   if ($i==2)
		   $hexdat.='</b></font>';
	   if ($i==4)
		$hexdat.='</font>';
   	   if ($i==8)
		$hexdat.='</font>';

   }
   echo '<pre><font color=gray><u>'.$label."</u></font>\n".$hexdat."\n".$value.'</pre>';

   getsome(0,$line);
   if (bin2hex($start=getsome(2))=='2929')
   {
	   echo 'start='.bin2hex($start).'<br>';
	   $command=getsome(1);
	   echo 'command= '.bin2hex($command).'<br>';
	   $length=hexdec(bin2hex(getsome(2)));
	   echo 'length= '.$length.'<br>';
   	   $termid=hexdec(bin2hex(getsome(4)));
	   echo 'terminal= '.$termid.'<br>';
	   $checksumdata=substr($line,3,6);
	   echo 'checksumdata='.bin2hex($checksumdata).'<Br>';
		if (bin2hex($command)=="80") {
			$location['time']=date("d-m-Y H:i:s",strtotime('20'.bin2hex(getsome(1)).'/'.bin2hex(getsome(1)).'/'.bin2hex(getsome(1)).' '.bin2hex(getsome(1)).':'.bin2hex(getsome(1)).':'.bin2hex(getsome(1))));

			$lat=bin2hex(getsome(4));
			$location['latitude']=substr($lat,0,3).','.substr($lat,3,strlen($lat)-3);
			$long=bin2hex(getsome(4));
			$location['longitude']=substr($long,0,3).','.substr($long,3,strlen($long)-3);
			$location['speed']=bin2hex(getsome(2));
			$location['direction']=bin2hex(getsome(2));
			$location['antenna']=bin2hex(getsome(1));
			$location['fuel']=bin2hex(getsome(1));
			getsome(2);
			$location['vehiclestate']=bin2hex(getsome(4));
			$location['otherstate']=bin2hex(getsome(8));
			echo '<pre>'.print_r($location,true).'</pre>';
		}



	   $checksum=$checksumdata[0];
	   for($i=1;$i<strlen($checksumdata);$i++) {
		   echo 'chk: '.bin2hex($checksum).' ^ '.bin2hex($checksumdata[$i]).' = '.bin2hex($checksum^$checksumdata[$i]).'<Br>';
		   $checksum^=$checksumdata[$i];
	   }
	   echo 'calculated checksum= '.bin2hex($checksum).'<br>';
   }
   echo '<br>';
}


?>
