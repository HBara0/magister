<?php

class Multipages {

    protected $total = 0;
    protected $limit = 0;
    protected $countby = '*';

    public function __construct($table, $limit, $where = '', $countby = '', $having = '') {
        $this->set_query($table, $where);
        $this->set_countby($countby);
        $this->set_total();
        $this->set_limit($limit);
    }

    protected function set_query($table, $where) {
        $this->querydata['table'] = $table;
        if (!empty($where)) {
            $this->querydata['where'] = ' WHERE ' . $where;
        }
    }

    protected function set_countby($countby) {
        if (!empty($countby)) {
            $this->countby = $countby;
        }
    }

    protected function set_total() {
        global $db;

        if ($this->countby != '*') {
            $this->countby = 'DISTINCT(' . $this->countby . ')';
        }
        $this->total = $db->fetch_field($db->query("SELECT COUNT({$this->countby}) as countrows FROM {$this->querydata[table]}{$this->querydata[where]}"), 'countrows');
    }

    protected function set_limit($limit) {
        $this->limit = intval(round($limit));
    }

    public function parse_multipages() {
        global $core, $lang;
        if ($this->total > 0) {
            if (!isset($core->settings['itemsperlist'])) {
                $core->settings['itemsperlist'] = 10;
            }

            if ($this->total > $core->settings['itemsperlist']) {
                $numberpages = ceil($this->total / $this->limit);
                $link = $_SERVER['REQUEST_URI'];

                if (!preg_match("/&start=[\d]/i", $link)) {
                    $link .= "&amp;start={1}";
                }

                if (isset($core->input['perpage'])) {
                    if (!strstr($link, 'perpage=' . $core->input['perpage'])) {
                        $link .= '&amp;perpage=' . $core->input['perpage'];
                    }
                }

                if (isset($core->input['filterby'])) {
                    if (!strstr($link, 'filterby=' . $core->input['filterby'])) {
                        $link .= '&amp;filterby=' . $core->input['filterby'];
                    }
                }

                if (isset($core->input['filtervalue'])) {
                    if (!strstr($link, 'filtervalue=' . $core->input['filtervalue'])) {
                        $link .= '&amp;filtervalue=' . $core->input['filtervalue'];
                    }
                }

                $current = $core->input['start'];
                $current_page = 1;
                if ($numberpages > 6) {
                    $break_multipages = true;
                    $current_page = ($current / $this->limit) + 1;

                    if ($current_page != 1) {
                        $prev_link = preg_replace("/&start=[\d]+/i", '&start=' . ($current - $this->limit), $link);
                        $links .= "<a href='{$prev_link}'>{$lang->prev}</a> ";
                    }

                    if ($current_page != $numberpages) {
                        if (strstr($link, '{1}')) {
                            $next_link = str_replace('{1}', $current + $this->limit, $link);
                        }
                        else {
                            $next_link = preg_replace("/&start=[\d]+/i", '&start=' . ($current + $this->limit), $link);
                        }
                        $links_next .= "<a href='{$next_link}'>{$lang->next}</a> ";
                    }

                    if ($current_page <= 3) {
                        $current_page -= $current_page - 1;
                    }

                    if ($current_page > 3) {
                        $current_page -= 3;
                    }

                    if ($current_page <= 0) {
                        $current_page = 1;
                    }
                }

                for ($i = $current_page; $i <= $numberpages; $i++) {
                    if (!$prev) {
                        if ($i == 1) {
                            $prev = 0;
                        }
                        else {
                            $prev = $this->limit * ($i - 1);
                        }
                    }

                    if ($break_multipages == true) {
                        if ($i > ($current_page + 5) && $i < ($numberpages - 2)) {
                            $i = $numberpages - 2;
                            $links .= '<span class="multipages">...</span> ';
                            $prev = $prev + ($this->limit * (($numberpages - 2) - ($current_page + 5)));
                            continue;
                        }
                    }

                    if (strstr($link, '{1}')) {
                        $ref = str_replace('{1}', $prev, $link);
                    }
                    else {
                        $ref = preg_replace("/&start=[\d]+/i", "&start={$prev}", $link);
                    }

                    if ($current == $prev) {
                        $links .= "<span class='multipages_current'>{$i}</span> ";
                    }
                    else {
                        $links .= "<a href='{$ref}'>{$i}</a> ";
                    }
                    $prev = $prev + $this->limit;
                }
                $links .= $links_next;
                return "<div class='multipages'>{$links}</div>";
            }
        }
        return false;
    }

}

?>