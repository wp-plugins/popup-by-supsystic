jQuery(document).ready(function(){
	var tblId = 'ppsPopupTbl';
	jQuery('#ppsPopupTbl').jqGrid({ 
		url: ppsTblDataUrl
	,	datatype: 'json'
	,	autowidth: true
	,	shrinkToFit: true
	,	colNames:[toeLangPps('ID'), toeLangPps('Label'), toeLangPps('Date'), toeLangPps('Action')]
	,	colModel:[
			{name: 'id', index: 'id', searchoptions: {sopt: ['eq']}, width: '30', align: 'center'}
		,	{name: 'label', index: 'ip', searchoptions: {sopt: ['eq']}, align: 'center'}
		,	{name: 'date_created', index: 'date_created', searchoptions: {sopt: ['eq']}, align: 'center'}
		,	{name: 'action', index: 'action', sortable: false, search: false, align: 'center'}
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
	,	caption: toeLangPps('Current Blacklist')
	,	height: '100%' 
	,	emptyrecords: toeLangPps('You have no data in blacklist for now.')
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
		var confirmMsg = listIds.length > 1
			? toeLangPps('Are you sur want to remove '+ listIds.length+ ' Pop-Ups?')
			: toeLangPps('Are you sur want to remove Pop-Up?')
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
function ppsPopupRemoveRow(id, link) {
	if(confirm(toeLangPps('Are you sure want to remove this Pop-Up?'))) {
		jQuery.sendFormPps({
			btn: link
		,	data: {mod: 'popup', action: 'remove', id: id}
		,	onSuccess: function(res) {
				if(!res.error) {
					jQuery('#ppsPopupTbl').trigger( 'reloadGrid' );
				}
			}
		});
	}
}