<?php
namespace booosta\smallcalendar;

use \booosta\Framework as b;
b::init_module('smallcalendar');

class Smallcalendar extends \booosta\calendar\Calendar
{
  use moduletrait_smallcalendar;

  protected $events_url, $multi_link;
  protected $hide_0000 = false;
  protected $id_prefix = 'smallcalendar';


  public function after_instanciation()
  {
    parent::after_instanciation();

    if(is_object($this->topobj) && is_a($this->topobj, "\\booosta\\webapp\\Webapp")):
      $this->topobj->moduleinfo['calendar'] = true;
      if($this->topobj->moduleinfo['jquery']['use'] == '') $this->topobj->moduleinfo['jquery']['use'] = true;
      if($this->topobj->moduleinfo['bootstrap']['use'] == '') $this->topobj->moduleinfo['bootstrap']['use'] = true;
    endif;
  }

  public function set_multi_link($link) { $this->multi_link = $link; }
  public function hide_0000($flag) { $this->hide_0000 = $flag; }

  public function get_htmlonly() { 
    return "<div id='$this->id'></div>";
  }

  public function get_js()
  {
    $eventlist = '';
    ksort($this->events);
    foreach($this->events as $event):
      $d = $event->get_data();
      $date = date('j/n/Y', strtotime($d['startdate']));

      if($this->hide_0000) $timestr = date('H:i', strtotime($d['startdate'])) == '00:00' ? '' : date('H:i', strtotime($d['startdate']));
      else $timestr = date('H:i', strtotime($d['startdate']));

      if($date == $prevdate):   // additional event on the same day
        $daylist .= $timestr . ' ' . $d['name'] . '<br>';
        $d['description'] = $daylist;
        $d['name'] = $this->t('Events on') . ' ' . date('d. m. Y', strtotime($d['startdate']));
        if($this->multi_link) $d['link'] = $this->multi_link;;
      else:
        $daylist = $timestr . ' ' . $d['name'] . '<br>';
        $d['name'] = $timestr . " {$d['name']}";
      endif;

      $eventlist .= "{ title: '{$d['name']}', date: '$date',";
      if($d['link']) $eventlist .= " link: '{$d['link']}',";
      if($d['link_target']) $eventlist .= " linkTarget: '{$d['link_target']}',";
      $eventlist .= " content: 'xxx{$d['description']} ',";
      if($d['settings']['color']) $eventlist .= " color: ''{$d['settings']['color']}',";
      $eventlist .= ' }, ';

      $prevdate = $date;
    endforeach;

    $ajaxcode = $this->events_url ? "reqAjax: { type: 'get', url: '$this->events_url' }" : '';

    if($this->t('month names') != 'month names') $monthNames = $this->t('month names');
    else $monthNames = "'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'";

    if($this->t('day names') != 'day names') $dayNames = $this->t('day names');
    else $dayNames = "'M', 'T', 'W', 'T', 'F', 'S', 'S'";

    if($this->date) $datestr = "date: '$this->date', "; else $datestr = '';

    $code = "
    var smallcalendar_monthNames = [$monthNames];
    var smallcalendar_dayNames = [$dayNames];
    var smallcalendar_events = [ $eventlist ];

    $('#$this->id').bic_calendar({
        events: smallcalendar_events, enableSelect: false, multiSelect: false, dayNames: smallcalendar_dayNames, $datestr
        monthNames: smallcalendar_monthNames, showDays: true, displayMonthController: true, displayYearController: false,                                
        $ajaxcode
    });";

    if(is_object($this->topobj) && is_a($this->topobj, "\\booosta\\webapp\\webapp")):
      $this->topobj->add_jquery_ready($code);
      return '';
    else:
      return "\$(document).ready(function(){ $code });";
    endif;
  }
}
