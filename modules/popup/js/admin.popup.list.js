jQuery(document).ready(function(){
	var tblId = 'ppsPopupTbl';
	jQuery('#'+ tblId).jqGrid({ 
		url: ppsTblDataUrl
	,	datatype: 'json'
	,	autowidth: true
	,	shrinkToFit: true
	,	colNames:[toeLangPps('ID'), toeLangPps('Label'), toeLangPps('Views'), toeLangPps('Unique Views'), toeLangPps('Actions'), toeLangPps('Conversion'), toeLangPps('Active')]
	,	colModel:[
			{name: 'id', index: 'id', searchoptions: {sopt: ['eq']}, width: '50', align: 'center'}
		,	{name: 'label', index: 'label', searchoptions: {sopt: ['eq']}, align: 'center'}
		,	{name: 'views', index: 'views', searchoptions: {sopt: ['eq']}, align: 'center'}
		,	{name: 'unique_views', index: 'unique_views', searchoptions: {sopt: ['eq']}, align: 'center'}
		,	{name: 'actions', index: 'actions', searchoptions: {sopt: ['eq']}, align: 'center'}
		,	{name: 'conversion', index: 'conversion', searchoptions: {sopt: ['eq']}, align: 'center'}
		,	{name: 'active', index: 'active', searchoptions: {sopt: ['eq']}, align: 'center'}
		]
	,	postData: {
			search: {
				text_like: jQuery('#'+ tblId+ 'SearchTxt').val()
			}
		}
	,	rowNum:10
	,	rowList:[10, 20, 30, 1000]
	,	pager: '#'+ tblId+ 'Nav'
	,	sortname: 'id'
	,	viewrecords: true
	,	sortorder: 'desc'
	,	jsonReader: { repeatitems : false, id: '0' }
	,	caption: toeLangPps('Current PopUp')
	,	height: '100%' 
	,	emptyrecords: toeLangPps('You have no PopUps for now.')
	,	multiselect: true
	,	onSelectRow: function(rowid, e) {
			var tblId = jQuery(this).attr('id')
			,	selectedRowIds = jQuery('#'+ tblId).jqGrid ('getGridParam', 'selarrrow')
			,	totalRows = jQuery('#'+ tblId).getGridParam('reccount')
			,	totalRowsSelected = selectedRowIds.length;
			if(totalRowsSelected) {
				jQuery('#ppsPopupRemoveGroupBtn').removeAttr('disabled');
				if(totalRowsSelected == totalRows) {
					jQuery('#cb_'+ tblId).prop('indeterminate', false);
					jQuery('#cb_'+ tblId).attr('checked', 'checked');
				} else {
					jQuery('#cb_'+ tblId).prop('indeterminate', true);
				}
			} else {
				jQuery('#ppsPopupRemoveGroupBtn').attr('disabled', 'disabled');
				jQuery('#cb_'+ tblId).prop('indeterminate', false);
				jQuery('#cb_'+ tblId).removeAttr('checked');
			}
			ppsCheckUpdate(jQuery(this).find('tr:eq('+rowid+')').find('input[type=checkbox].cbox'));
			ppsCheckUpdate('#cb_'+ tblId);
		}
	,	gridComplete: function(a, b, c) {
			var tblId = jQuery(this).attr('id');
			jQuery('#ppsPopupRemoveGroupBtn').attr('disabled', 'disabled');
			jQuery('#cb_'+ tblId).prop('indeterminate', false);
			jQuery('#cb_'+ tblId).removeAttr('checked');
			if(jQuery('#'+ tblId).jqGrid('getGridParam', 'records'))	// If we have at least one row - allow to clear whole list
				jQuery('#ppsPopupClearBtn').removeAttr('disabled');
			else
				jQuery('#ppsPopupClearBtn').attr('disabled', 'disabled');
			// Custom checkbox manipulation
			ppsInitCustomCheckRadio('#'+ jQuery(this).attr('id') );
			ppsCheckUpdate('#cb_'+ jQuery(this).attr('id'));
		}
	,	loadComplete: function() {
			var tblId = jQuery(this).attr('id');
			if (this.p.reccount === 0) {
				jQuery(this).hide();
				jQuery('#'+ tblId+ 'EmptyMsg').show();
			} else {
				jQuery(this).show();
				jQuery('#'+ tblId+ 'EmptyMsg').hide();
			}
		}
	});
	jQuery('#'+ tblId+ 'NavShell').append( jQuery('#'+ tblId+ 'Nav') );
	jQuery('#'+ tblId+ 'Nav').find('.ui-pg-selbox').insertAfter( jQuery('#'+ tblId+ 'Nav').find('.ui-paging-info') );
	jQuery('#'+ tblId+ 'Nav').find('.ui-pg-table td:first').remove();
	jQuery('#'+ tblId+ 'SearchTxt').keyup(function(){
		var searchVal = jQuery.trim( jQuery(this).val() );
		if(searchVal && searchVal != '') {
			ppsGridDoListSearch({
				text_like: searchVal
			}, tblId);
		}
	});
	
	jQuery('#'+ tblId+ 'EmptyMsg').insertAfter(jQuery('#'+ tblId+ '').parent());
	jQuery('#'+ tblId+ '').jqGrid('navGrid', '#'+ tblId+ 'Nav', {edit: false, add: false, del: false});
	jQuery('#cb_'+ tblId+ '').change(function(){
		jQuery(this).attr('checked') 
			? jQuery('#ppsPopupRemoveGroupBtn').removeAttr('disabled')
			: jQuery('#ppsPopupRemoveGroupBtn').attr('disabled', 'disabled');
	});
	jQuery('#ppsPopupRemoveGroupBtn').click(function(){
		var selectedRowIds = jQuery('#ppsPopupTbl').jqGrid ('getGridParam', 'selarrrow')
		,	listIds = [];
		for(var i in selectedRowIds) {
			var rowData = jQuery('#ppsPopupTbl').jqGrid('getRowData', selectedRowIds[ i ]);
			listIds.push( rowData.id );
		}
		var popupLabel = '';
		if(listIds.length == 1) {	// In table label cell there can be some additional links
			var labelCellData = ppsGetGridColDataById(listIds[0], 'label', 'ppsPopupTbl');
			popupLabel = jQuery(labelCellData).text();
		}
		var confirmMsg = listIds.length > 1
			? toeLangPps('Are you sur want to remove '+ listIds.length+ ' PopUps?')
			: toeLangPps('Are you sure want to remove "'+ popupLabel+ '" PopUp?')
		if(confirm(confirmMsg)) {
			jQuery.sendFormPps({
				btn: this
			,	data: {mod: 'popup', action: 'removeGroup', listIds: listIds}
			,	onSuccess: function(res) {
					if(!res.error) {
						jQuery('#ppsPopupTbl').trigger( 'reloadGrid' );
					}
				}
			});
		}
		return false;
	});
	jQuery('#ppsPopupClearBtn').click(function(){
		if(confirm(toeLangPps('Clear whole popup list?'))) {
			jQuery.sendFormPps({
				btn: this
			,	data: {mod: 'popup', action: 'clear'}
			,	onSuccess: function(res) {
					if(!res.error) {
						jQuery('#ppsPopupTbl').trigger( 'reloadGrid' );
					}
				}
			});
		}
		return false;
	});
	
	ppsInitCustomCheckRadio('#'+ tblId+ '_cb');
});
