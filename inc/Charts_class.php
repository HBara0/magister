<?php

include('pChart/class/pData.class.php');
include('pChart/class/pDraw.class.php');
include('pChart/class/pPie.class.php');
include('pChart/class/pImage.class.php');

class Charts {

    private $data = array();
    private $options = array();
    //protected $chart = '';
    private $path = './tmp/charts/';
    private $font = 'arial.ttf';
    private $fonts_dir = 'fonts/';

    public function __construct(array $data, $type = 'line', array $options = array()) {
        $this->font = ROOT . INC_ROOT . '/fonts/tahoma.ttf';

        $this->data = $data;
        $this->options = $options;

        if ($type == 'pie') {
            //$this->data = $this->normalizedata($this->data, 'values', 'titles');
            $this->build_piechart();
        }
        elseif ($type == 'bar') {
            //$this->data = $this->normalizedata($this->data, 'y', 'x');
            $this->build_barchart();
        }
        elseif ($type == 'stackedbar') {
            $this->build_stackedbar();
        }
        else {
            $this->build_linechart();
        }
        $this->clean_oldcharts();
    }

    private function build_piechart() {
        /* Create and populate the pData object */
        $this->DataSet = new pData();
        $this->DataSet->addPoints($this->data['values'], 'Values');
        $this->DataSet->addPoints($this->data['titles'], 'Labels');
        $this->DataSet->setSerieDescription('Values', 'Values');

        /* Define the absissa serie */
        $this->DataSet->setAbscissa('Labels');

        /* Create the pChart object */
        $this->chart = new pImage(450, 200, $this->DataSet, TRUE);

        /* Set the default font properties */
        $this->chart->setFontProperties(array('FontName' => $this->font, 'FontSize' => 10, 'R' => 80, 'G' => 80, 'B' => 80));

        /* Create the pPie object */
        $PieChart = new pPie($this->chart, $this->DataSet);

        /* Enable shadow computing */
        $this->chart->setShadow(TRUE, array('X' => 3, 'Y' => 3, 'R' => 0, 'G' => 0, 'B' => 0, 'Alpha' => 10));

        /* Draw a splitted pie chart */
        $PieChart->draw3DPie(170, 90, array('Radius' => 100, 'DataGapAngle' => 12, 'DataGapRadius' => 10, 'Border' => TRUE, 'DrawLabels' => TRUE));

        /* Write the legend box */
        $this->chart->setFontProperties(array('FontName' => $this->font, 'FontSize' => 7, 'R' => 0, 'G' => 0, 'B' => 0));
        $PieChart->drawPieLegend(330, 15, array('Style' => LEGEND_NOBORDER, 'Mode' => LEGEND_VERTICAL));

        $this->imagename = $this->path . 'chart_' . uniqid(rand(0, time())) . '.png';

        /* Render the picture (choose the best way) */
        $this->chart->render($this->imagename);
    }

    private function build_barchart() {
        $this->DataSet = new pData();
        foreach ($this->data['y'] as $index => $rawdata) {
            if (is_array($rawdata)) {
                foreach ($rawdata as $index2 => $val) {
                    $this->ready_data[$index2][] = $val;
                }
            }
            else {
                $this->ready_data['y'][$index] = $rawdata;
            }
        }
        foreach ($this->ready_data as $legend => $bar) {
            ksort($bar);
            $this->DataSet->addPoints($bar, $legend);
        }
        $this->DataSet->setAxisName(0, $this->options['yaxisname']);
        $this->DataSet->SetAxisUnit(0, $this->options['yaxisunit']);
        $this->DataSet->setAxisName(1, $this->options['xaxisname']);

        if (!isset($this->options['xaxisnosort']) || $this->options['xaxisnosort'] == false) {
            ksort($this->data['x']);
        }

        $this->DataSet->addPoints($this->data['x'], 'x');
        $this->DataSet->setSerieDescription("x", $this->options['xaxisname']);
        $this->DataSet->setAbscissa('x');

        /* Create the pChart object */
        if (!isset($this->options['width']) || empty($this->options['width'])) {
            $this->options['width'] = 700;
        }

        if (!isset($this->options['height']) || empty($this->options['height'])) {
            $this->options['height'] = 230;
        }
        $this->chart = new pImage($this->options['width'], $this->options['height'], $this->DataSet);
        /* Draw one static threshold area */
        if (isset($this->options['treshholdsettings']) && !empty($this->options['treshholdsettings'])) {
            $this->chart->drawXThresholdArea($this->options['treshholdsettings']['firstindex'], $this->options['treshholdsettings']['secondindex'], array('R' => 226, 'G' => 194, 'B' => 54, 'Alpha' => 20));
        }
        /* Enable/Disable Antialiasing */
        $this->chart->Antialias = FALSE;
        if ($this->options['antialias'] == TRUE) {
            $this->chart->Antialias = TRUE;
            if (!empty($this->options['antialiasquality'])) {
                $this->chart->AntialiasQuality = intval($this->options['antialiasquality']);
            }
        }
        /* Write the chart title */
        if (isset($this->options['title']) && !empty($this->options['title'])) {
            $this->chart->setFontProperties(array('FontName' => $this->font, 'FontSize' => 11));
            $this->chart->drawText(50, 30, $this->options['title'], array('FontSize' => 15, 'Align' => TEXT_ALIGN_BOTTOMLEFT));
        }
        /* Set the default font */
        $this->chart->setFontProperties(array('FontName' => $this->font, 'FontSize' => 8));

        /* Define the chart area */
        $grapharea['x1position'] = 50;
        if (isset($this->options['x1position'])) {
            $grapharea['x1position'] = $this->options['x1position'];
        }
        $this->chart->setGraphArea($grapharea['x1position'], 30, 680, 200);

        /* Draw the scale */
        if (isset($this->options['labelrotationangle'])) {
            $scaleSettings['LabelRotation'] = $this->options['labelrotationangle'];
        }
        $scaleSettings = array('GridR' => 150, 'GridG' => 150, 'GridB' => 150, 'DrawSubTicks' => TRUE, 'CycleBackground' => TRUE, 'Mode' => SCALE_MODE_FLOATING, 'LabelRotation' => $scaleSettings['LabelRotation']);

        if (isset($this->options['scale']) && !empty($this->options['scale'])) {
            switch ($this->options['scale']) {
                case SCALE_START0: $this->options['scale'] = SCALE_MODE_START0;
                    break;
                case SCALE_ADDALL: $this->options['scale'] = SCALE_MODE_ADDALL;
                    break;
                case SCALE_NORMAL: $this->options['scale'] = SCALE_MODE_FLOATING;
                    break;
                default: break;
            }
            $scaleSettings['Mode'] = $this->options['scale'];
        }
        if (isset($this->options['scalepos'])) {
            $scaleSettings['Pos'] = SCALE_POS_TOPBOTTOM;
        }

        $this->chart->drawScale($scaleSettings);

        /* Write the chart legend */
        if ($this->options['noLegend'] == false) {
            $this->chart->drawLegend(75, 20, array('Style' => LEGEND_NOBORDER, 'Mode' => LEGEND_HORIZONTAL));
            //$this->chart->drawLegend(596, 150, array('Style' => LEGEND_NOBORDER, 'Mode' => LEGEND_VERTICAL));
        }

        /* Turn on shadow computing */
        $this->chart->setShadow(TRUE, array('X' => 1, 'Y' => 1, 'R' => 0, 'G' => 0, 'B' => 0, 'Alpha' => 10));

        /* Draw the chart */
        //$this->chart->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
        //$settings = array("Gradient"=>TRUE,"GradientMode"=>GRADIENT_EFFECT_CAN,"DisplayPos"=>LABEL_POS_INSIDE,"DisplayValues"=>TRUE,"DisplayR"=>255,"DisplayG"=>255,"DisplayB"=>255,"DisplayShadow"=>TRUE,"Surrounding"=>10);

        $this->chart->drawBarChart();

        $this->imagename = $this->path . 'chart_' . uniqid(rand(0, time())) . '.png';

        /* Render the picture (choose the best way) */
        $this->chart->render($this->imagename);
    }

    private function build_linechart() {
        $this->DataSet = new pData();

        if (!isset($this->options['seriesweight'])) {
            $this->options['seriesweight'] = 4;
        }

        foreach ($this->data['y'] as $legend => $line) {
            if (count($line) == 1) {
                $line[0] = 0;
            }

            //ksort($line);
            $this->DataSet->addPoints($line, $legend);
            $this->DataSet->setSerieWeight($legend, $this->options['seriesweight']);
            if (isset($this->options['linescolors'][$legend]) && is_array($this->options['linescolors'][$legend])) {
                $this->DataSet->setPalette($legend, $this->options['linescolors'][$legend]);
            }
        }

        $this->DataSet->setAxisName(0, $this->options['yaxisname']);
        $this->DataSet->SetAxisUnit(0, $this->options['yaxisunit']);
        $this->DataSet->setAxisName(1, $this->options['xaxisname']);

        if (count($this->data['x']) == 1) {
            $this->data['x'][0] = '';
        }
        if (!isset($this->options['nosort']) || $this->options['nosort'] == false) {
            ksort($this->data['x']);
        }
        $this->DataSet->addPoints($this->data['x'], 'x');
        $this->DataSet->setSerieDescription('x', $this->options['xaxisname']);
        $this->DataSet->setAbscissa('x');

        /* Create the pChart object */
        if (!isset($this->options['width']) || empty($this->options['width'])) {
            $this->options['width'] = 700;
        }

        if (!isset($this->options['height']) || empty($this->options['height'])) {
            $this->options['height'] = 250;
        }
        $this->chart = new pImage($this->options['width'], $this->options['height'], $this->DataSet);

        /* Write the chart title */
        if (isset($this->options['title']) && !empty($this->options['title'])) {
            $this->chart->setFontProperties(array('FontName' => $this->font, 'FontSize' => 11));
            $this->chart->drawText(150, 35, $this->options['title'], array('FontSize' => 20, 'Align' => TEXT_ALIGN_BOTTOMMIDDLE));
        }

        /* Set the default font */
        $this->chart->setFontProperties(array('FontName' => $this->font, 'FontSize' => 8));

        /* Define the chart area */

        if (!isset($this->options['graphareax2margin'])) {
            $this->options['graphareax2margin'] = 20;
        }

        if (!isset($this->options['graphareay2margin'])) {
            $this->options['graphareay2margin'] = 20;
        }

        $this->chart->setGraphArea(70, 30, $this->options['width'] - $this->options['graphareax2margin'], $this->options['height'] - $this->options['graphareay2margin']);

        /* Draw the scale */
        $scaleSettings = array('XMargin' => 10, 'YMargin' => 10, 'Floating' => TRUE, 'GridR' => 150, 'GridG' => 150, 'GridB' => 150, 'DrawSubTicks' => TRUE, 'CycleBackground' => TRUE);
        if (is_array($this->options['fixedscale']) && !empty($this->options['fixedscale'])) {
            $scaleSettings['ManualScale'] = array(0 => array('Min' => $this->options['fixedscale']['min'], 'Max' => $this->options['fixedscale']['max']));
        }
        if (isset($this->options['labelrotationangle'])) {
            $scaleSettings['LabelRotation'] = $this->options['labelrotationangle'];
        }

        if (isset($this->options['scale']) && !empty($this->options['scale'])) {
            switch ($this->options['scale']) {
                case SCALE_START0: $this->options['scale'] = SCALE_MODE_START0;
                    break;
                case SCALE_ADDALL: $this->options['scale'] = SCALE_MODE_ADDALL;
                    break;
                case SCALE_NORMAL: $this->options['scale'] = SCALE_MODE_FLOATING;
                    break;
                default: break;
            }
            $scaleSettings['Mode'] = $this->options['scale'];
        }
        $this->chart->drawScale($scaleSettings);

        /* Draw one static threshold area */
        if (isset($this->options['treshholdsettings']) && !empty($this->options['treshholdsettings'])) {
            $this->chart->drawXThresholdArea($this->options['treshholdsettings']['firstindex'], $this->options['treshholdsettings']['secondindex'], array('R' => 226, 'G' => 194, 'B' => 54, 'Alpha' => 20));
        }
        /* Enable/Disable Antialiasing */
        $this->chart->Antialias = FALSE;
        if ($this->options['antialias'] == TRUE) {
            $this->chart->Antialias = TRUE;
            if (!empty($this->options['antialiasquality'])) {
                $this->chart->AntialiasQuality = intval($this->options['antialiasquality']);
            }
        }

        /* Draw the line chart */
        $this->chart->drawLineChart();
        /**/

        /* Write a label */
        if ($this->options['writelabel'] == true) {
            if (!isset($this->options['label_series'])) {
                $this->options['label_series'] = array_keys($this->data['y']);
            }

            if (isset($this->options['label_seriesindexes'])) {
                $this->chart->writeLabel($this->options['label_series'], $this->options['label_seriesindexes'], array("DrawVerticalLine" => TRUE));
            }
        }

        /* Write the chart legend */
        $this->chart->drawLegend(75, 20, array('Style' => LEGEND_NOBORDER, 'Mode' => LEGEND_HORIZONTAL));


        if (isset($this->options['path'])) {
            $this->set_path($this->options['path']);
        }
        $this->imagename = $this->path . 'chart_' . uniqid(rand(0, time())) . '.png';

        /* Render the picture (choose the best way) */
        $this->chart->render($this->imagename);
    }

    public function build_stackedbar() {
        $this->DataSet = new pData();

        foreach ($this->data['y'] as $legend => $series) {
            if ($this->options['seriesnames']) {
                $legend = $this->options['seriesnames'][$legend];
            }
            $this->DataSet->addPoints($series, $legend);
        }

        $this->DataSet->setAxisName(0, $this->options['yaxisname']);
        $this->DataSet->SetAxisUnit(0, $this->options['yaxisunit']);

        //ksort($this->data['x']);
        $this->DataSet->addPoints($this->data['x'], 'x');
        $this->DataSet->setSerieDescription('x', $this->options['xaxisname']);
        $this->DataSet->setAbscissa('x');


        /* Create the pChart object */
        /* Create the pChart object */
        if (!isset($this->options['width']) || empty($this->options['width'])) {
            $this->options['width'] = 700;
        }

        if (!isset($this->options['height']) || empty($this->options['height'])) {
            $this->options['height'] = 250;
        }
        $this->chart = new pImage($this->options['width'], $this->options['height'], $this->DataSet);

        /* Set the default font properties */
        $this->chart->setFontProperties(array('FontName' => $this->font, 'FontSize' => 6));

        /* Draw the scale and the chart */
        $this->chart->setGraphArea(70, 30, $this->options['width'] - 20, $this->options['height'] - 20);

        $scale_settings = array('DrawSubTicks' => TRUE, 'Mode' => SCALE_MODE_ADDALL_START0, 'Pos' => SCALE_POS_LEFTRIGHT);
        if ($this->options['orientation'] == 'horizontal') {
            $scale_settings['Pos'] = SCALE_POS_TOPBOTTOM;
        }
        /* Draw one static threshold area */
        if (isset($this->options['treshholdsettings']) && !empty($this->options['treshholdsettings'])) {
            $this->chart->drawXThresholdArea($this->options['treshholdsettings']['firstindex'], $this->options['treshholdsettings']['secondindex'], array('R' => 226, 'G' => 194, 'B' => 54, 'Alpha' => 20));
        }
        $this->chart->drawScale($scale_settings);
        $this->chart->setShadow(FALSE);
        $stackbar_settings = array('Surrounding' => -15, 'InnerSurrounding' => 15, 'DisplayValues' => 1);
        if ($this->options['displayvalues'] == false) {
            $stackbar_settings['DisplayValues'] = 0;
        }
        $this->chart->drawStackedBarChart($stackbar_settings);

        /* Write a label */
        if ($this->options['writelabel'] == true) {
            if (!isset($this->options['label_series'])) {
                $this->options['label_series'] = array_keys($this->data['y']);
            }

            if (isset($this->options['label_seriesindexes'])) {
                $this->chart->writeLabel($this->options['label_series'], $this->options['label_seriesindexes'], array("DrawVerticalLine" => TRUE));
            }
        }

        /* Write the chart legend */
        $this->chart->drawLegend(75, 20, array('Style' => LEGEND_NOBORDER, 'Mode' => LEGEND_HORIZONTAL));

        $this->imagename = $this->path . 'chart_' . uniqid(rand(0, time())) . '.png';

        /* Render the picture (choose the best way) */
        $this->chart->render($this->imagename);
    }

    public function get_chart() {
        return $this->imagename;
    }

    private function normalizedata(array $array = array(), $values_index = '', $titles_index = '') {
        if (empty($array)) {
            $array = $this->data;
        }

        if (isset($values_index) && !empty($values_index)) {
            ksort($array[$values_index]);
            $values_tocheck = $array[$values_index];
            $newarray_tofill = &$new_array[$values_index];
        }
        else {
            ksort($array);
            $values_tocheck = $array;
            $newarray_tofill = &$new_array;
        }
        $i = 0;
        foreach ($values_tocheck as $key => $val) {
            if (is_array($val)) {
                $val = $this->normalizedata($val);
            }
            $newarray_tofill[$i] = $val;
            $i++;
        }


        if (isset($titles_index) && !empty($titles_index)) {
            $i = 0;
            foreach ($array[$titles_index] as $key => $val) {
                $new_array[$titles_index][$i] = $val;
                $i++;
            }
        }
        return $new_array;
    }

    private function array_replace_keys(array $array) {
        arsort($array['values']);

        $i = 0;
        foreach ($array['values'] as $key => $val) {
            $new_array['values'][$i] = $val;
            if (isset($array['titles'])) {
                $new_array['titles'][$i] = $array['titles'][$key];
            }
            $i++;
        }

        return $new_array;
    }

    public function set_path($path) {
        global $core;
        $this->path = $path;
        //$this->path = $core->sanitize_path($path);
    }

    public function delete_chartfile() {
        @unlink($this->imagename);
    }

    private function remove_values_spaces() {
        foreach ($this->data['values'] as $key => $val) {
            $this->data['values'][$key] = str_replace(' ', '', strval($val));
        }
    }

    private function clean_oldcharts() {
        global $core;

        if (is_dir($this->path)) {
            $dir = opendir($this->path);
            while (false !== ($file = readdir($dir))) {
                $file_info = pathinfo($this->path . '/' . $file);
                if ($file != '.' && $file != '..' && $file_info['extension'] == 'png') {
                    if (TIME_NOW - filemtime($this->path . '/' . $file) > (60 * 60)) {
                        @unlink($this->path . $file);
                    }
                }
            }
            closedir($dir);
            clearstatcache();
        }
    }

}

?>