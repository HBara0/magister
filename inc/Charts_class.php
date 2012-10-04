<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Charts Class
 * $id: Charts_class.php
 * Last Update: @zaher.reda 	July 11, 2012 | 09:53 AM
 */

include("pChart/pData.class");  
include("pChart/pChart.class"); 
class Charts {
	private $data = array();
	private $options = array();
	//protected $chart = '';
	private $path = './images/charts/';
	private $font = 'arial.ttf';
	private $fonts_dir = 'fonts/';
	
	public function __construct(array $data, $type='line', array $options = array()) {
		$this->font = ROOT.INC_ROOT.'/fonts/tahoma.ttf';

		$this->data = $data;
		$this->options = $options;
				
		if($type == 'pie') {
			//$this->data = $this->normalizedata($this->data, 'values', 'titles');
			$this->build_piechart();
		}
		elseif($type == 'bar') {
			//$this->data = $this->normalizedata($this->data, 'y', 'x');
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
		$this->DataSet->AddPoint($this->data['values'], 'Serie1');  
		$this->DataSet->AddPoint($this->data['titles'], 'Serie2');  
		$this->DataSet->AddAllSeries();  
		$this->DataSet->SetAbsciseLabelSerie('Serie2');  
		
		#  // Initialise the graph  
		$this->chart = new pChart(450,200);
		$this->chart->loadColorPalette(ROOT.INC_ROOT.'/pChart/tones.txt');  
		$this->chart->AntialiasQuality = 20;
		$this->chart->setFontProperties($this->font, 8);  
		
		$this->chart->drawPieGraph($this->DataSet->GetData(),$this->DataSet->GetDataDescription(), 170, 90, 130, PIE_PERCENTAGE, TRUE, 50, 20, 5);  
		$this->chart->drawPieLegend(330, 15, $this->DataSet->GetData(), $this->DataSet->GetDataDescription(), 250, 250, 250);  
		#   
  		$this->imagename = $this->path.'chart_'.uniqid(rand(0, time())).'.png';
		$this->chart->Render($this->imagename);
		//$this->chart = $image;	
	}
	
	private function build_barchart() {
		//ksort($this->data['y']);
		
		foreach($this->data['y'] as $index => $rawdata) {
			if(is_array($rawdata)) {
				foreach($rawdata as $index2 => $val) {
					$this->ready_data[$index2][] = $val;
				}
			}
			else
			{
				$this->ready_data['y'][$index] = $rawdata;
			}
		}

		$this->DataSet = new pData; 
		$i = 1;
		foreach($this->ready_data as $legend => $bar) {
			ksort($bar);
			$this->DataSet->AddPoint($bar, 'Serie'.$i);
			$this->DataSet->SetSerieName($legend, 'Serie'.$i);
			$i++;
		}

		$this->DataSet->AddAllSeries(); 

		ksort($this->data['x']);
		$this->DataSet->AddPoint($this->data['x'], 'x');
		$this->DataSet->SetAbsciseLabelSerie('x'); 
		
		// Initialise the graph  
		$this->chart = new pChart(700,230); 
		$this->chart->AntialiasQuality = 100;
		$this->chart->setFontProperties($this->font,8);  
		$this->chart->setGraphArea(50,30,680,200);  

		$this->chart->drawGraphArea(255,255,255,TRUE);
		if(!isset($this->options['scale']) || empty($this->options['scale'])) {
			$this->options['scale'] = SCALE_NORMAL;
		}

		$this->chart->drawScale($this->DataSet->GetData(),$this->DataSet->GetDataDescription(), $this->options['scale'], 150,150,150,TRUE,0,2,TRUE);     
		$this->chart->drawGrid(4,TRUE,230,230,230,50);  
		
		// Draw the 0 line  
		$this->chart->setFontProperties($this->font,6);  
		$this->chart->drawTreshold(0,143,55,72,TRUE,TRUE);  
		
		// Draw the bar graph  
		$this->chart->drawBarGraph($this->DataSet->GetData(),$this->DataSet->GetDataDescription(),TRUE);  
		
		// Finish the graph  
		$this->chart->setFontProperties($this->font,8);
		if($this->options['noLegend'] == false) {
			$this->chart->drawLegend(596,150,$this->DataSet->GetDataDescription(),255,255,255);  
		}
		$this->imagename = $this->path.'chart_'.uniqid(rand(0, time())).'.png';
		$this->chart->Render($this->imagename);
	}
	
	private function build_linechart() {
		$this->DataSet = new pData;     
		
		$i = 1;
		foreach($this->data['y'] as $legend => $line) {
			if(count($line) == 1) {
				$line[0] = 0;
			}
			ksort($line);
			$this->DataSet->AddPoint($line, 'Serie'.$i);
			$this->DataSet->SetSerieName($legend,'Serie'.$i);
			$this->DataSet->AddSerie('Serie'.$i);
			$i++; 
		}
   		
		if(count($this->data['x']) == 1) {
			$this->data['x'][0] = '';
		}
		ksort($this->data['x']);
		$this->DataSet->AddPoint($this->data['x'], 'x');
		$this->DataSet->SetAbsciseLabelSerie('x');     
  
		$this->DataSet->SetYAxisName($this->options['yaxisname']);
		$this->DataSet->SetYAxisUnit($this->options['yaxisunit']);
		$this->DataSet->SetXAxisName($this->options['xaxisname']); 
  
		/* Initialise the graph */
		if(!isset($this->options['width']) || empty($this->options['width'])) {
			$this->options['width'] = 700;
		}
		
		if(!isset($this->options['height']) || empty($this->options['height'])) {
			$this->options['height'] = 250;
		}
		
		$this->chart = new pChart($this->options['width'], $this->options['height']);
		$this->chart->AntialiasQuality = 25;   
		$this->chart->setFontProperties($this->font, 8);     
		$this->chart->setGraphArea(70, 30, $this->options['width']-20,  $this->options['height']-20);     
   
		$this->chart->drawGraphArea(255, 255, 255, TRUE);
		
		if(is_array($this->options['fixedscale']) && !empty($this->options['fixedscale'])) {
			$this->chart->setFixedScale($this->options['fixedscale']['min'], $this->options['fixedscale']['max']);
		}
		
		$this->chart->drawScale($this->DataSet->GetData(), $this->DataSet->GetDataDescription(), SCALE_NORMAL, 150, 150, 150, TRUE, 0, 2);     
		$this->chart->drawGrid(4,TRUE,230,230,230,50);  
		   
		/*  Draw the 0 line   */  
		$this->chart->setFontProperties($this->font,6);     
		$this->chart->drawTreshold(0,143,55,72,TRUE,TRUE);     
		   
		/*  Draw the line graph    */ 
		$this->chart->drawLineGraph($this->DataSet->GetData(),$this->DataSet->GetDataDescription());     
		$this->chart->drawPlotGraph($this->DataSet->GetData(),$this->DataSet->GetDataDescription(),3,2,255,255,255);     
		    
		/*  Finish the graph   */     
		$this->chart->setFontProperties($this->font,8);     
		$this->chart->drawLegend(75,35,$this->DataSet->GetDataDescription(),255,255,255);         
		
		$this->imagename = $this->path.'chart_'.uniqid(rand(0, time())).'.png';
		$this->chart->Render($this->imagename);
	}
	
	public function get_chart() {
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