<?php
namespace booosta\smallcalendar;

\booosta\Framework::add_module_trait('webapp', 'smallcalendar\webapp');

trait webapp
{
  protected function preparse_smallcalendar()
  {
    $libpath = 'vendor/booosta/smallcalendar/src';
    if($this->moduleinfo['calendar']):
      $this->add_includes("<script type='text/javascript' src='{$this->base_dir}$libpath/bic_calendar.js'></script>
            <link rel='stylesheet' type='text/css' href='{$this->base_dir}$libpath/bic_calendar.css' />");
    endif;
  }
}
