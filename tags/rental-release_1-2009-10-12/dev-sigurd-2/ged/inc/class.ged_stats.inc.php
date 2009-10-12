<?php
	/**************************************************************************
	* phpGroupWare - ged
	* http://www.phpgroupware.org
	* Written by Pascal Vilarem <pascal.vilarem@steria.org>
	*
	* --------------------------------------------------------------------------
	*  This program is free software; you can redistribute it and/or modify it
	*  under the terms of the GNU General Public License as published by the
	*  Free Software Foundation; either version 2 of the License, or (at your
	*  option) any later version
	***************************************************************************/
	
include(PHPGW_SERVER_ROOT.'/ged/inc/class.PHPlot.inc.php');
	
class ged_stats
{
	var $public_functions=array(
	'ged_pie_status'=>true,
	'ged_bar_activities'=>true
	);
	
	function ged_stats()
	{
		$this->ged_dm=CreateObject('ged.ged_dm', True);	
	}	
	
	function ged_pie_status()
	{
		$data = array(
		  array('', 10),
		  array('', 20),
		  array('', 30),
		  array('', 35),
		  array('',  5)
		);
		
		$this->plotter =& new PHPlot(450,200);
		$this->plotter->SetImageBorderType('plain');
		$this->plotter->SetDataType('text-data-single');
		$this->plotter->SetDataValues($data);
		$this->plotter->SetPlotType('pie');
		
		$colors = array( '#ffff78', '#ffff00', '#e586fc', 'green', '#008000', 'red');
		$legend = array( 'working', 'tech rev', 'quality rev', 'delivered', 'approved', 'rejected');
		
		$this->plotter->SetDataColors($colors);
		$this->plotter->SetLegend($legend);
		$this->plotter->SetShading(14);
		$this->plotter->SetLabelScalePosition(0.2);
		

		//Draw it
		$this->plotter->DrawGraph();				
	}
	
	function ged_bar_activities()
	{

		//Define the object
		$graph =& new PHPlot(400,250);
		
		$graph->SetPrintImage(0); //Don't draw the image until specified explicitly
		
		$example_data = array(
		     array('a',30,40,20),
		     array('b',50,'',10),  // here we have a missing data point, that's ok
		     array('c',70,20,60),
		     array('d',80,10,40),
		     array('e',20,40,60),
		     array('f',60,40,50),
		     array('g',70,20,30)
		);
		
		$graph->SetDataType("text-data");  //Must be called before SetDataValues
		
		$graph->SetDataValues($example_data);
		
		$graph->SetXTitle("");
		$graph->SetYTitle("Verbal Cues");
		$graph->SetYTickIncrement(10);
		$graph->SetPlotType("bars");
		$graph->SetXLabelAngle(0);  //have to re-set as defined above
		
		//$graph->SetNewPlotAreaPixels(70,120,375,220);
		//$graph->SetPlotAreaWorld(0,0,7,80);
		$graph->DrawGraph();
		
		//Print the image
		$graph->PrintImage();
	}
	
}