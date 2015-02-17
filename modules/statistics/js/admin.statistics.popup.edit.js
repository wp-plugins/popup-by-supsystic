var g_ppsCurrentPlot = null
,	g_ppsCurrentChartType = ''
,	g_ppsPieAllActionDone = false
,	g_ppsPieAllShareDone = false;
jQuery(document).ready(function(){
	jQuery('.ppsPopupStatChartTypeBtn').click(function(){
		ppsUpdatePopupStatsGraph( jQuery(this).data('type') );
		return false;
	});
	jQuery('#ppsPopupStatClear').click(function(){
		if(confirm(toeLangPps('Are you sure want to clear all PopUp Statistics?'))) {
			jQuery.sendFormPps({
				btn: this
			,	data: {mod: 'statistics', action: 'clearForPopUp', id: jQuery(this).data('id')}
			,	onSuccess: function(res) {
					if(!res.error) {
						toeReload();
					}
				}
			});
		}
		return false;
	});
});
function ppsPopupStatGetChartType() {
	var savedValue = getCookiePps('pps_chart_coockie');
	return savedValue && savedValue != '' ? savedValue : 'line';
}
function ppsPopupStatSetChartType(type) {
	jQuery('.ppsPopupStatChartTypeBtn').removeClass('focus');
	jQuery('.ppsPopupStatChartTypeBtn[data-type="'+ type+ '"]').addClass('focus');
	setCookiePps('pps_chart_coockie', type);
}
function ppsDrawPopupCharts() {
	ppsUpdatePopupStatsGraph();
	ppsUpdateAllActionChart();
	ppsUpdateAllShareChart();
}
function ppsUpdateAllActionChart() {
	if(!g_ppsPieAllActionDone) {
		if(typeof(ppsPopupAllStats) != 'undefined') {
			var	plotDataByCode = {}
			,	haveData = false;
			for(var i = 0; i < ppsPopupAllStats.length; i++) {
				if(ppsPopupAllStats[i]['points'] 
					&& ppsPopupAllStats[i]['points'].length 
					&& !toeInArrayPps(ppsPopupAllStats[i]['code'], ['show'])	// make sure - this was exactly action, not like just display
				) {
					var labelCode = ppsPopupAllStats[i].label.replace(/\W+/g, "_");
						plotDataByCode[ labelCode ] = {label: ppsPopupAllStats[i].label, total: 0};
					for(var j = 0; j < ppsPopupAllStats[i]['points'].length; j++) {
						plotDataByCode[ labelCode ].total += parseInt(ppsPopupAllStats[ i ]['points'][ j ]['total_requests']);
					}
					haveData = true;
				}
			}
			if(haveData) {
				var plotData = [];
				for(var code in plotDataByCode) {
					plotData.push([ plotDataByCode[code].label, plotDataByCode[code].total ]);
				}
				jQuery.jqplot ('ppsPopupStatAllActionsPie', [ plotData ], {
					seriesDefaults: {
						renderer: jQuery.jqplot.PieRenderer
					,	rendererOptions: {
							showDataLabels: true
						}
					}
				,	legend: { show:	true, location: 'e' }
				});
			} else {
				jQuery('#ppsPopupStatAllActionsNoData').show();
			}
		}
		g_ppsPieAllActionDone = true;
	}
}
function ppsUpdateAllShareChart() {
	if(!g_ppsPieAllShareDone) {
		if(typeof(ppsPopupAllShareStats) != 'undefined') {
			var plotData = [];
			for(var i = 0; i < ppsPopupAllShareStats.length; i++) {
				if(ppsPopupAllShareStats[i].sm_type) {
					plotData.push([ ppsPopupAllShareStats[i].sm_type.label, parseInt(ppsPopupAllShareStats[i].total_requests) ]);
				}
			}
			if(plotData.length) {
				jQuery.jqplot ('ppsPopupStatAllSharePie', [ plotData ], {
					seriesDefaults: {
						renderer: jQuery.jqplot.PieRenderer
					,	rendererOptions: {
							showDataLabels: true
						}
					}
					,	legend: { show:	true, location: 'e' }
				});
			} else {
				jQuery('#ppsPopupStatAllShareNoData').show();
			}
		} else
			jQuery('#ppsPopupStatAllShareNoData').show();
		g_ppsPieAllShareDone = true;
	}
}
function ppsUpdatePopupStatsGraph(chartType) {
	if(typeof(ppsPopupAllStats) != 'undefined') {
		chartType = chartType ? chartType : ppsPopupStatGetChartType();
		if(g_ppsCurrentChartType == chartType) {
			// Just switching tabs - no need to redraw if it is already drawn
			return;
		}
		ppsPopupStatSetChartType( chartType );
		g_ppsCurrentChartType = chartType;
		var plotData = []
		,	seriesKeys = {}
		,	series = [];
		
		if(g_ppsCurrentPlot) {
			g_ppsCurrentPlot.destroy();
		}
		switch(chartType) {
			case 'bar':
				var ticksKeys = {}
				,	ticks = []
				,	tickId = 0
				,	sortByDateClb = function(a, b) {
					var aTime = ( new Date( str_replace((typeof(a) === 'string' ? a : a.date), '-', '/') ) ).getTime()	// should be no "-" as ff make it Date.parse() in incorrect way
					,	bTime = ( new Date( str_replace((typeof(b) === 'string' ? b : b.date), '-', '/') ) ).getTime();
					if(aTime > bTime)
						return 1;
					if(aTime < bTime)
						return -1;
					return 0;
				}
				,	plotDataToDate = [];
				for(var i = 0; i < ppsPopupAllStats.length; i++) {
					if(ppsPopupAllStats[i]['points'] && ppsPopupAllStats[i]['points'].length) {
						plotDataToDate.push({});
						for(var j = ppsPopupAllStats[i]['points'].length - 1; j >= 0; j--) {
							ticksKeys[ ppsPopupAllStats[ i ]['points'][ j ]['date'] ] = 1;
							plotDataToDate[ tickId ][ ppsPopupAllStats[ i ]['points'][ j ]['date'] ] = parseInt(ppsPopupAllStats[ i ]['points'][ j ]['total_requests']);
						}
						seriesKeys[ tickId ] = ppsPopupAllStats[i].label;
						tickId++;
					}
				}
				for(var key in ticksKeys) {
					ticks.push( key );
				}
				ticks.sort( sortByDateClb );
				tickId = 0;
				for(var i = 0; i < plotDataToDate.length; i++) {
					plotData.push([]);
					for(var j in ticks) {
						var dateStr = ticks[ j ];
						plotData[ tickId ].push( typeof(plotDataToDate[i][dateStr]) === 'undefined' ? 0 : plotDataToDate[i][dateStr] );
					}
					tickId++;
				}
				for(var i in seriesKeys) {
					series.push({label: seriesKeys[ i ]});
				}
				g_ppsCurrentPlot = jQuery.jqplot('ppsPopupStatGraph', plotData, {
					seriesDefaults:{
						renderer: jQuery.jqplot.BarRenderer
					,	rendererOptions: {fillToZero: true}
					,	pointLabels: { 
							show: true 
						}
					}
				,	series: series
				,	legend: { show:	true, location: 'ne' }
				,	axes: {
						xaxis: {
							renderer: jQuery.jqplot.CategoryAxisRenderer
						,	ticks: ticks
						},
						yaxis: {
							pad: 1.05
						,	tickOptions: {
								formatString: '%d'
							}
						}
					}
				,	highlighter: {
						show: true
					,	sizeAdjust: 3
					,	tooltipLocation: 'n'
					,	tooltipContentEditor: function(str, seriesIndex, pointIndex, jqPlot) {
							if(seriesKeys[ seriesIndex ]) {
								if(strpos(str, ',')) {
									str = str.split(',');
									str = str[1] ? str[1] : str[0];
									str = jQuery.trim(str);
								}
								return seriesKeys[ seriesIndex ]+ ' ['+ str+ ']';
							}
							return str;
						}
					}
				,	cursor: {
						show: true
					,	zoom: true
					}
				});
				
				break;
			case 'line':
			default:
				var tickId = 0;
				for(var i = 0; i < ppsPopupAllStats.length; i++) {
					if(ppsPopupAllStats[i]['points'] && ppsPopupAllStats[i]['points'].length) {
						plotData.push([]);
						for(var j = 0; j < ppsPopupAllStats[i]['points'].length; j++) {
							plotData[ tickId ].push([ppsPopupAllStats[ i ]['points'][ j ]['date'], parseInt(ppsPopupAllStats[ i ]['points'][ j ]['total_requests'])]);
						}
						seriesKeys[ tickId ] = ppsPopupAllStats[i].label;
						tickId++;
					}
				}
				for(var i in seriesKeys) {
					series.push({label: seriesKeys[ i ]});
				}
				g_ppsCurrentPlot = jQuery.jqplot('ppsPopupStatGraph', plotData, {
					axes: {
						xaxis: {
							label: toeLangPps('Date')
						,	labelRenderer: jQuery.jqplot.CanvasAxisLabelRenderer
						,	renderer:	jQuery.jqplot.DateAxisRenderer
						,	tickOptions:{formatString:'%b %#d, %Y'},
						}
					,	yaxis: {
							label: toeLangPps('Requests')
						,	labelRenderer: jQuery.jqplot.CanvasAxisLabelRenderer
						}
					}
				,	series: series
				,	legend: { show:	true, location: 'ne' }
				,	highlighter: {
						show: true
					,	sizeAdjust: 7.5
					,	tooltipContentEditor: function(str, seriesIndex, pointIndex, jqPlot) {
							if(seriesKeys[ seriesIndex ]) {
								return seriesKeys[ seriesIndex ]+ ' ['+ str+ ']';
							}
							return str;
						}
					}
				,	cursor: {
						show: true
					,	zoom: true
					}
				});
				break;
		}
	}
}