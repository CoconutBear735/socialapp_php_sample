
var owner_id;
var viewer_id;
var timer_id;

var f_owner = false;
var f_viewer = false;

var f_act1 = false;

$(document).ready(function() {
	
	$(window).autoHeight();
        
	$.ajax({
		url: '/people/@owner/@self',
		data: { fields: 'userHash' },
		dataType: 'data',
		success: function(people) {
			//console.dir(people);
			
			var owner = people[0];
			owner_id = owner.id;
			
			f_owner = true;
		},
		error: function(xhr, status, e) {
			//console.info(xhr, status, e);
		}
	});
	
	$.ajax({
		url: '/people/@viewer/@self',
		//data: { appData: 'start_act' },
		data: {},
		dataType: 'data',
		success: function(people) {
			//console.dir(people);
			
			var viewer = people[0];
			viewer_id = viewer.id;
			
			f_viewer = true;
			//f_act1 = viewer.appData.start_act;
		},
		error: function(xhr, status, e) {
			//console.info(xhr, status, e);
		}
	});
	
	$.ajax({
		url: '/people/@viewer/@friends/@app',
		data: { startIndex: 0 },
		dataType: 'data',
		success: function(people) {
			//console.dir(people);
		},
		error: function(xhr, status, e) {
			//console.info(xhr, status, e);
		}
	});
	
	timer_id = setInterval('excute_flash()', 100);
});

function excute_flash()
{
	if ((f_owner) && (f_viewer)){
	
		clearInterval(timer_id);
		
		if (owner_id === viewer_id) {
			
			if (mode === NORMAL) {

				link_banner_change();
				
				$('#container').flash(SWF, {
					id:'main_flash',
					name:'main_flash',
					width: 760,
					height: 600,
					allowScriptAccess: 'always'
				});
			}
			else if (mode === MAINTENANCE) {
				$("#menu").empty();
				$("#container").html("<span>只今、メンテナンス中です。</span>");
			}
			else {
				$("#container").html("<span></span>");
			}
			
			$("#footer").html("<span>socialapp_php_sample</span>");
		}
		else {
			parent.location.href = APPLI_URL;
		}
	}
}

function SendActivity(_title, _bIsComm)
{
	var params = { };
	params[opensocial.Activity.Field.TITLE] = '' + _title;
	
	var activity = opensocial.newActivity(params);
	
	opensocial.requestCreateActivity(activity,
					_bIsComm == true ? opensocial.CreateActivityPriority.HIGH : opensocial.CreateActivityPriority.LOW,
					function(response) {
						if (response.hadError()) {
							var errCode = response.getErrorCode();
							//console.info(errCode);
						}
						else {}
					});
}

function get_presistence()
{
	$.ajax({
		url: '/appdata/@viewer/@self',
		data: { fields: 'start_act' },
		dataType: 'data',
		success: function(data) {
		
			var act_check = false;
			
			$.each(data, function(userId, data) {
				//console.info(userId);
				//console.info(data);
				act_check = data.start_act;
			});
			
			if (!act_check) {
				SendActivity(APPLI_NAME + 'をはじめました。', false);
				set_presistence();
			}
		},
		error: function(xhr, status, e) {
			//console.error(xhr, status, e);
		}
	});
}

function set_presistence()
{
	$.ajax({
		type: 'post',
		url: '/appdata/@viewer/@self',
		data: {
			start_act: true
		},
		dataType: 'data',
		success: function() {},
		error: function(xhr, status, e) {
			//console.error(xhr, status, e);
		}
	});
}

function makeSignedRequestJ(controller, method, caller, as_data)
{
	//console.info(controller, method, caller, as_data);
	
	var address = new String;
	address += SITE_URL + controller + '/' + method;
	
	var postData = new Object();
	postData.id = 1;
	
	if (typeof(as_data) !== "undefined") {
	
		if (as_data instanceof Object) {
			
			for (var key in as_data) {
				postData[key] = as_data[key];
			}
		}
	}
	else {
		postData.id = 0;
	}
	
	$.ajax({
		url: address,
		type: 'post',
		data: postData,
		dataType: 'json',
		oauth: 'signed',
		success: function(data, status) {
			
			var flash=document.main_flash||window.main_flash;
			flash.as_func(200, caller, data);
			
			//$("#output").empty();
			//var ppTable = prettyPrint(data);
			//console.dir(ppTable);
			//$("#output").append(ppTable);
		},
		error: function(xhr, status, e) {
		
			var rc = xhr.status;
			
			//console.info(xhr, rc, status, e);
			
			$("#container").empty();
			$("#container").html("<span>通信エラーが発生しました。</span><br /><span>" + xhr.responseText + "</span>");
			
			$("#menu").empty();
		}
	});
}

function friend_invite()
{
	$.invite(function(ids) {
		$.ajax({
			type: 'post',
			url: '/appdata/@viewer/@self',
			data: {
				inviteIds:ids,
				last_update: new Date().getTime(),
				feeling: 'well',
				footprint: true
			},
			dataType: 'data',
			success: function() {},
			error: function(xhr, status, e) {
				//console.error(xhr, status, e);
			}
		});
	});
}

<!-- mix payment api -->
function make_mixi_payment(item_id)
{
	var address = new String;
	address += SITE_URL + 'mixi_payment/index';
	
	var postData = new Object();
	postData.id = 1;
	postData.item_id = item_id;
	
	$.ajax({
		url: address,
		type: 'post',
		data: postData,
		dataType: 'json',
		oauth: 'signed',
		success: function(data, status) {
			call_mixi_payment(data)
		},
		error: function(xhr, status, e) {
			$("#container").empty();
			$("#container").html("<span>通信エラーが発生しました。</span><br /><span>" + xhr.responseText + "</span>");
			$("#menu").empty();
		}
	});
}

function call_mixi_payment(data)
{	
	var params = {};

	params[opensocial.Payment.Field.AMOUNT] = data.item_price;
	params[mixi.Payment.Field.ITEM_NAME] = data.item_name;
	params[mixi.Payment.Field.SIGNATURE] = data.signature;
	params[mixi.Payment.Field.ITEM_ID] = data.item_id
	;
	params[mixi.Payment.Field.IS_TEST] = data.is_test;
	params[mixi.Payment.Field.INVENTORY_CODE] = data.inventory_code
	params[opensocial.Payment.Field.PAYMENT_TYPE] = opensocial.Payment.PaymentType.PAYMENT;
	
	//console.info(params);
	
	var payment = opensocial.newPayment(params);
	
	opensocial.requestPayment(payment, data.callback_url, function(response) {
		
		var code = 200;
		var ret = true;
		//console.info(response);
    	if (response.hadError()) {
			ret = false;
        	code = response.getErrorCode();
        	if (code == opensocial.Payment.ResponseCode.USER_CANCELLED) {
        	}
			else {
        	}
    	}
		else {
        	//var data = response.getData();
			
			//$("#output").empty();
			//var ppTable = prettyPrint(data);
			//$("#output").append(ppTable);
    	}
		
		var flash=document.main_flash||window.main_flash;
		flash.callback_payment(code,ret);
	});
}

<!-- mbge payment api -->
function call_mbge_payment(item_info)
{
	//console.info(item_info);
	var itemParams = {};
	itemParams[opensocial.BillingItem.Field.SKU_ID] = item_info['item_id'];
	itemParams[opensocial.BillingItem.Field.PRICE]  = item_info['price'];
	itemParams[opensocial.BillingItem.Field.COUNT]  = item_info['count'];
	itemParams[mbga.BillingItem.Field.NAME] = item_info['name'];
	itemParams[mbga.BillingItem.Field.IMAGE_URL] = item_info['image_url'];
	var item = opensocial.newBillingItem(itemParams);
	
	var params = {};
	params[opensocial.Payment.Field.ITEMS]  = [item];
	params[opensocial.Payment.Field.AMOUNT] = item_info['price'] * item_info['count'];
	var payment = opensocial.newPayment(params);
	opensocial.requestPayment(payment, function(response) {
		var code = 200;
		var ret = true;
		//console.info(code, ret);
		if (response.hadError()) {
			ret = false;
        	code = response.getErrorCode();
		}
		else {
		}
		
		var flash=document.main_flash||window.main_flash;
		flash.callback_payment(code, ret);
	});
}
