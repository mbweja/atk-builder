<?php


namespace App\Modules;

use Sintattica\Atk\Core\Node;

class AtkBuilderNode extends Node
{
  function adminHeader()
	{
    $script='<script type="text/javascript" src="./atk/javascript/newwindow.js"></script> ';
		$filter_bar=$this->getAdminFilterBar();
		$view_bar=$this->getAdminViewBar();
		$script.="<br><table width=100%><tr><td width=50%>$filter_bar</td><td align=right>$view_bar</td></tr></table><br>";
		return $script; 
	}

  function adminFooter()
  {
    return '';
	}

	function getAdminFilterBar()
	{
		if ( (!isset($this->admin_filters)) || (!is_array($this->admin_filters)))
		return "";
		$max_filters = count($this->admin_filters) -1;
		$a = $this->getAdminFilter();
		@$cur_filter = $a['cur_filter'];
		$prev_filter = ($cur_view - 1 ) < 0 ? $max_filters : $cur_filter - 1;
		$next_filter = ($cur_view + 1 ) > $max_filters ? 0 : $cur_filter + 1;
		$bar=href(dispatch_url($this->atknodetype(),'admin', array('filter_nbr' => $prev_filter)),"<<")." ";
		for($i=0;$i <= $max_filters ;$i++)
		{
			$a = href(dispatch_url($this->atknodetype(),'admin', array('filter_nbr' => $i)),$this->admin_filters[$i][0])." ";
			if ($i == $cur_filter)
				$a = "<b><i>".$a."</i></b>";
			$bar.=$a;
		}
		$bar  .= href(dispatch_url($this->atknodetype(),'admin', array('filter_nbr' => $next_filter)),">>");
		return "Mostrar: ".$bar;
	}
	
	function getAdminViewBar()
	{
		if ( (!isset($this->admin_views)) || (!is_array($this->admin_views)))
			return "";
		$max_views = count($this->admin_views) -1;
		$cur_view = $this->getAdminView();
		$prev_view = ($cur_view - 1 ) < 0 ? $max_views : $cur_view - 1;
		$next_view = ($cur_view + 1 ) > $max_views ? 0 : $cur_view + 1;
		$bar=href(dispatch_url($this->atknodetype(),'admin', array('view_nbr' => $prev_view)),"<<")." ";
		for($i=0;$i <= $max_views ;$i++)
		{
			$a = href(dispatch_url($this->atknodetype(),'admin', array('view_nbr' => $i)),$i)." ";
			if ($i == $cur_view)
				$a = "<b><i>".$a."</i></b>";
			$bar.=$a;
		}
		$bar  .= href(dispatch_url($this->atknodetype(),'admin', array('view_nbr' => $next_view)),">>");
		return "Vistas: ".$bar;
	}
	
	function getAdminView()
	{
		global $g_sessionManager;
		$cur_view = $g_sessionManager->stackVar('view_nbr');
		if ($cur_view == NULL)
			$cur_view = 0;
		return $cur_view;	
	}
	
	function getAdminFilter()
	{
		global $g_sessionManager;
		$cur_filter = $g_sessionManager->stackVar('filter_nbr');
		if ($cur_filter == NULL)
		$cur_filter = 0;
		return $cur_filter;
	}
	
	function setAdminView()
	{
		if ( (!isset($this->admin_views)) || (!is_array($this->admin_views)))
			return;
		
		$cur_view = $this->getAdminView();
		$attributes = $this->getAttributeNames();
		foreach ($attributes as $name)
			$this->getAttribute($name)->addFlag(AF_HIDE_LIST|AF_FORCE_LOAD);
		foreach ($this->admin_views[$cur_view] as $name)
			$this->getAttribute($name)->removeFlag(AF_HIDE_LIST);
	}
	
	function setAdminFilter()
	{
		if ( (!isset($this->admin_filters)) || (!is_array($this->admin_filters)))
			return;
		$cur_filter = $this->getAdminFilter();
		$this->addFilter($this->admin_filters[$cur_filter][1]);
	}
	
	function action_admin(&$handler, $record=null)
	{
		$this->setAdminView();
		$this->setAdminFilter();
		return $handler->action_admin($record);
	}

}

?>
