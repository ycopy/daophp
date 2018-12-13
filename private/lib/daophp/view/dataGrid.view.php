<?php
?>


<style type="text/css">
	.actionBannerTop{height:35px;width:100%;overflow:auto;/*border:1px solid gray;*/margin-bottom:5px;}
	.gridContainer{width:100%;overflow:auto;/*border:1px solid gray*/}
</style>
<div id="gridContainer" class="gridContainer" dojoType="dijit.layout.ContentPane" com="<?php echo  $comName; ?>" table="<?php echo  $tableName; ?>">
	This message is loaded by xhr
</div>

<script type="text/javascript">
	
	//console.log('loadOk');
	var targetCom = dojo.attr(dojo.byId('gridContainer'),'com');
	var targetTable = dojo.attr(dojo.byId('gridContainer'),'table');

	if ( assertEmpty(targetCom) || assertEmpty(targetTable)) {
		console.log('targetCom or targetTable could be null');
	}
	
	
	//console.log( targetTable );

	var gridObj = new EPGrid( {
		id: 'gridContainer',
		url: '?com='+targetCom+'&__actiongetDojoDataStore',
		CDA:{},
		targetTable:targetTable,
		targetCom: targetCom,
		gridType:'standard'
	} );
	
	gridObj.exec();
	
//	var content = {
//		CDA:{},
//		target: 'menu',
//	}
	
//	
//	new EPGrid('gridContainer');
//    var xhrPost_getMenuDataStore = {
//        //in this case , we could not assign a response_type
//        url: '?com=menu&__action=getDojoDataStore',
//        content: content,
//        handleAs: 'json',
//        load: function(data){
//            console.log(data);
//            //data.dataStore = new dojo.data.ItemFileWriteStore({data:data.dataStore});
//            dojo.publish('gridContainer/update', [data]);
//        },
//        error: function(errData){
//            console.log('xhrPost error ' + errData);
//        }
//    };
//    dojo.xhrPost(xhrPost_getMenuDataStore);
	
</script>


