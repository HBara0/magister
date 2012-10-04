<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Charts Class
 * $id: Charts_class.php
 * Last Update: @zaher.reda 	May 12, 2009 | 12:50 PM
 */

include("pChart/pData.class");  
include("pChart/pChart.class"); 
class Charts {
	private $data = array();
	//protected $chart = '';
	private $path = './images/charts/';
	private $font = 'arial.ttf';

	public function __construct(array $data, $type='line') {
		$this->font = ROOT.INC_ROOT.'/fonts/arial.ttf';
				
		$this->data = $data;
				
		if($type == 'pie') {
			$this->data = $this->normalizedata($this->data, 'values', 'titles');
			$this->build_piechart();
		}
		elseif($type == 'bar') {
			$this->data = $this->normalizedata($this->data, 'y', 'x');
			$this->build_barchart();	
		}
		else
		{
			$this->build_linechart();
		}
		$this->clean_oldcharts();
	}
	
	private function build_piechart() {
		$this->DataSet = new pData;  
		$this->DataSet->AddPoint($this->data['values'],"Serie1");  
		$this->DataSet->AddPoint($this->data['titles'],"Serie2");  
		$this->DataSet->AddAllSeries();  
		$this->DataSet->SetAbsciseLabelSerie("Serie2");  
		
		#  // Initialise the graph  
		$this->chart = new pChart(400,220);  
		$this->chart->drawFilledRoundedRectangle(7,7,373,193,5,240,240,240);  
		$this->chart->drawRoundedRectangle(5,5,375,195,5,230,230,230);  
		#   
		#  // Draw the pie chart  
		//$this->chart->setFontProperties("Fonts/tahoma.ttf", 8);  
		$this->chart->setFontProperties($this->font, 8);  
		
		$this->chart->drawPieGraph($this->DataSet->GetData(),$this->DataSet->GetDataDescription(),150,90,110,PIE_PERCENTAGE,TRUE,50,20,5);  
		$this->chart->drawPieLegend(310,15,$this->DataSet->GetData(),$this->DataSet->GetDataDescription(),250,250,250);  
		#   
  		$this->imagename = $this->path.'chart_'.uniqid(rand(0, time())).'.png';

		
		$this->chart->Render($this->imagename);
		//$this->chart = $image;	
	}
	
	private function build_barchart() {
		$height = 200;
		$width = $height*2.3;
		$image = imagecreate($width, $height);
		
		$chart_boundary = $height * 0.1;
		
		$white = imagecolorallocate($image, 255, 255, 255);
		$black = imagecolorallocate($image, 0, 0, 0);
		$red = imagecolorallocate($image, 255, 0, 0);
		
		$zeroX = $chart_boundary;
		$zero = $zeroY = $height - $chart_boundary;
		
		imagesetthickness($image, 2);
		imageline($image, $zeroX, $zeroY, $width-$chart_boundary, $zero, $black);
		imageline($image, $zeroX, $zeroY, $chart_boundary, 0, $black);
		
		$numlines = count($this->data['x']);
		//$max_func = create_function('$data', 'foreach($data as $key => $val) { if($max < max($val)) $max = max($val); } return $max;');
		//$array_count_func = create_function('$data', 'foreach($data as $key => $val) { $counts[] = count($data[$key]); } return $counts;');
		
		//$numvalues = max($array_count_func($this->data['x']));
		
		for($i=0;$i<=$numlines;$i++) {
			$color[$i] = imagecolorallocate($image, mt_rand(0, 235), mt_rand(5, 245), mt_rand(10, 255));
		}
		
		//$percent = 100/$numvalues;
		$increment_by = ($width-$chart_boundary)/($numlines*2);///($percent*($height*2))/100;
		//echo ($width-$chart_boundary).'/'.($numlines*2).'='.($width-$chart_boundary)/($numlines*2).' | '.$increment_by;
		//echo "<br />";
		//$endx = $chart_boundary+$increment_by;
		//$endy = 0;
		$max = max($this->data['y']); //max()
		
		imagettftext($image, 10, 0, 5, $zeroY-5, $black, $this->font, '0');
		
		imagesetthickness($image, 3);
		//echo "max: {$max} <br />";
		$yratio = $max/$zeroY;
		for($i=0;$i<=$numlines;$i++) {
			if($i == 0) {
				$startx = $increment_by;
				$starty = $zeroY - $this->data['y'][$i];
				
				//if($this->data['y'][$i] > $height) {
					$starty = $zeroY - ($this->data['y'][$i]/$yratio);
				//}
				
				//$starty = ((((abs($zeroY - $this->data['y'][$i]))*100)/$zeroY)*$zeroY)/100;
				$endx = $startx + $increment_by;
				$endy = $zeroY;
			}
			else
			{
				$startx = $endx + $increment_by;
				$starty = $zeroY - $this->data['y'][$i];
				//if($this->data['y'][$i] > $height) {
						$starty = $zeroY - ($this->data['y'][$i]/$yratio);
				//}
				//$starty = $zeroY-((((abs($zeroY - $this->data['y'][$i])*100))/$zeroY)*$zeroY)/100;
				$endx = $startx  + $increment_by;
				$endy = $zeroY;
			}
			
			if($this->data['y'][$i] == $max) {
				imagettftext($image, 10, 0, 5, $starty, $black, $this->font, $max);
				$maxy = $starty;
			}

			imagettftext($image, 10/$yratio, 270, $endx-($increment_by/2), $zero+8, $black, $this->font, $this->data['x'][$i]);
			
			imagettftext($image, 10, 0, $endx-($increment_by/2), $starty-8, $black, $this->font, $this->data['y'][$i]);
			//echo $i.": statx".$startx.' - starty '.$starty.'- endx '.$endx.' endy '.$endy."<br />";
			//
			imagefilledrectangle($image, $startx, $starty, $endx, $endy, $color[$i]);
		}
		
		imagettftext($image, 10, 0, 5, $zeroY/2, $black, $this->font, $max/2);
		imagettftext($image, 10, 0, 5, 10, $black, $this->font, ceil($max+($max*((($maxy*100)/$zeroY)/100))));
		
		$this->chart = $image;
	}
	
	private function build_linechart() {
		$height = 350;
		$width = $height*2.3;
		$image = imagecreate($width, $height);
		
		$chart_boundary = $height * 0.1;
		
		$white = imagecolorallocate($image, 255, 255, 255);
		$black = imagecolorallocate($image, 0, 0, 0);
		$red = imagecolorallocate($image, 255, 0, 0);
		$grey = imagecolorallocate($image, 235, 235, 235); 
		
		$zeroX = $chart_boundary;
		$zero = $zeroY = $height - $chart_boundary;
		
		imagesetthickness($image, 2);
		imageline($image, $zeroX, $zeroY, $width-$chart_boundary, $zero, $black);
		imageline($image, $zeroX, $zeroY, $chart_boundary, 0, $black);
		
		$max_func = create_function('$data', 'foreach($data as $key => $val) { if($max < max($val)) $max = max($val); } return $max;');
		$array_count_func = create_function('$data', 'foreach($data as $key => $val) { $counts[] = count($data[$key]); } return $counts;');
		
		$numlines = count($this->data['y']);
		$numvalues = max($array_count_func($this->data['y']));
		
		for($i=0;$i<$numlines;$i++) {
			$color[$i] = imagecolorallocate($image, mt_rand(0, 235), mt_rand(5, 245), mt_rand(10, 255));
		}
		
		$percent = 100/$numvalues;
		//$increment_by = ($percent*($width*2))/100;
		
		$increment_by = (($width-($width* 0.1))/count($this->data['x']))-2;
		$endx = $chart_boundary+$increment_by;
		$endy = 0;
		$max = $max_func($this->data['y']);
		
		imagettftext($image, 10, 0, 5, $zeroY-5, $black, $this->font, '0');
		
		imagesetthickness($image, 3);
		
		$yratio = $max/$zeroY;
		$i = 0;
		//for($i=0;$i<$numlines;$i++) {
		foreach($this->data['y'] as $legend => $line) {
		$this->data['y'][$i] = $this->normalizedata($line);
			/*if(empty($legend_prev_x2)) {
				$legend_prev_x2 = $zeroX+(pow($i+1, 2)*$yratio);
			}
			imageline($image, $legend_prev_x2 + 10, $zero+3, $legend_prev_x2 + 20, $zero+3, $color[$i]);
			imagettftext($image, 5, 0, $legend_prev_x2 + 20, $zero+3, $black, $this->font, 'name');
			$legend_prev_x2 = $legend_prev_x2 + 20;
			*/
			//continue;
			if($numvalues > count($this->data['y'][$i])) {
				$stop_at = count($this->data['y'][$i]);
			}
			
			$endx = $chart_boundary+$increment_by;
			for($q=0;$q<$numvalues;$q++) {
				if($this->data['y'][$i][$q] == $max) {
					$placement = 0;
				}
				else
				{
					$placement = 15;
					if((($this->data['y'][$i][$q]*100)/$max) > 90) {
						$placement = -5;
					}
				}
				
				if($q == 0) {
					$startx = $zeroX;
					$starty = $zeroY;
					//$endy = @round($zero-($zeroY*($this->data['y'][$i][$q]*100/$max))/100);
					$endy = $zeroY - @($this->data['y'][$i][$q]/$yratio);
					imagettftext($image, 7, 0, ($endx-$startx)/2, $zeroY - (($starty-$endy)/2), $color[$i], $this->font, $legend);

					imagestring($image, 4, $endx+2, $endy-$placement, $this->data['y'][$i][$q], $black);
				}
				else
				{
					if(isset($stop_at)) {
						if($q >= $stop_at) {
							continue;
						}
					}
					$startx = $endx;
					$endx += $increment_by;
					//$starty = @round($zero-($zeroY*($this->data['y'][$i][$q-1]*100/$max))/100);
					$starty = $zeroY - @($this->data['y'][$i][$q-1]/$yratio);
					$endy = $zeroY - @($this->data['y'][$i][$q]/$yratio);
					
					//$endy = @round($zero-($zeroY*($this->data['y'][$i][$q]*100/$max))/100);
					imagestring($image, 4, $endx+2, $endy-$placement, $this->data['y'][$i][$q], $black);
				}
				imageline($image, $startx, $starty, $endx, $endy, $color[$i]);
			}
			$i++;
		}
			
		$endx = $chart_boundary;
		//for($i=1;$i<=$numlines;$i++) {
		imagesetthickness($image, 1);
			//for($q=0;$q<=$numvalues;$q++) {
			$this->data['x'] = $this->normalizedata($this->data['x']);
			foreach($this->data['x'] as $xaxis) {
				$endx += $increment_by;
				imageline($image, $endx, 0, $endx, $zeroY, $grey);
				imagettftext($image, 10, 0, $endx-2, $zero+11, $black, $this->font, $xaxis);
			}
		//}
		
		imagettftext($image, 10, 0, 5, $height/2, $black, $this->font, $max/2);
		imagettftext($image, 10, 0, 5, 10, $black, $this->font, $max);
		
		//Draw diagonal line
		//imageline($image, $zeroX, $zeroY, $endx, $endy, $black);
		
		$this->chart = $image;	
	}
	
	public function get_chart() {
		//$imagename = $this->path.'chart_'.uniqid(rand(0, time())).'.png';
		/*@unlink($imagename);
		@touch($imagename);
		@imagepng($this->chart, $imagename);
		@imagedestroy($this->chart);*/
		
		//$this->chart->Render($imagename);
		return $this->imagename;
	}
	
	private function normalizedata(array $array = array(), $values_index = '', $titles_index = '') {
		if(empty($array)) {
			$array = $this->data;
		}
				
		if(isset($values_index) && !empty($values_index)) {
			ksort($array[$values_index]);
			$values_tocheck = $array[$values_index];
			$newarray_tofill = &$new_array[$values_index];
		}
		else
		{
			ksort($array);
			$values_tocheck = $array;
			$newarray_tofill = &$new_array;
		}
			$i = 0;
			foreach($values_tocheck as $key => $val) {
				if(is_array($val)) {
					$val = $this->normalizedata($val);
				}
				$newarray_tofill[$i] = $val;
				$i++;
			}

		
		if(isset($titles_index) && !empty($titles_index)) {
			$i = 0;
			foreach($array[$titles_index] as $key => $val) {
				$new_array[$titles_index][$i] = $val;
				$i++;
			}
		}
		return $new_array;
	}
	
	private function array_replace_keys(array $array) {
		arsort($array['values']);

		$i = 0;
		foreach($array['values'] as $key => $val) {
			$new_array['values'][$i] = $val;
			if(isset($array['titles'])){
				$new_array['titles'][$i] = $array['titles'][$key];
			}
			$i++;
		}

		return $new_array;
	}
	
	private function remove_values_spaces() {
		foreach($this->data['values'] as $key => $val) {
			$this->data['values'][$key] = str_replace(' ', '', strval($val));
		}
	}
	
	private function clean_oldcharts() {
		global $core;

		if(is_dir($this->path)) {
			$dir = opendir($this->path);
			while (false !== ($file = readdir($dir))) {
				if ($file != '.' && $file != '..') {
					if(time() - filemtime($this->path.$file) > (60*$core->settings['idletime'])) {
						@unlink($this->path.$file);
					} 
				}
			}
			closedir($dir);
			clearstatcache();
		}
	}
}
?>