
var timer_id;

$(document).ready(function() {

	get_friend_ranking('home', 'friend_ranking', 1);
	
	//make_mixi_payment(1);
	
	timer_id = setInterval('excute_flash()', 100);
});

function excute_flash()
{	
	clearInterval(timer_id);
	
	if (mode === NORMAL) {
		
		str = navigator.userAgent;
		
		if ((str.indexOf('Android 2.2') != -1) || (str.indexOf('Android 2.3') != -1)) {
		
			$("#container").flashembed({
				src: TOUCH_SWF,
				id: 'main_flash',
				width: 320,
				height: 317 }
			);
			
		}
		else {
			$("#container").html("<span>非対応端末です。</span>");
		}
	}
	else if (mode === MAINTENANCE) {
		$("#container").empty();
		$("#container").html("<span>只今、メンテナンス中です。</span>");
	}
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
			
			if (typeof(data.UNSUPPORT) === "undefined") {
				var flash=document.main_flash||window.main_flash;
				flash.as_func(200, caller, data);
			}
			else {
				$("#container").empty();
				$("#container").html("<span>非対応端末です。</span>");
			}			
		},
		error: function(xhr, status, e) {
		
			var rc = xhr.status;
			
			//console.info(xhr, rc, status, e);
			
			$("#container").empty();
			$("#container").html("<span>通信エラーが発生しました。</span><br /><span>" + xhr.responseText + "</span>");
		}
	});
}

function friend_invite()
{
	opensocial.requestShareApp(  
		"VIEWER_FRIENDS",  
	  	null,
	  	function(data) {
			if (data.hadError()) {
				 var errCode = data.getErrorCode();
			}
			else {
				var invited = data.getData();
				
				if (invited !== false) {}
			}  
	  	}
	);
}

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
		}
	});
}

function call_mixi_payment(data)
{	
	var params = {};

	params[opensocial.Payment.Field.AMOUNT] = data.item_price;
	params[mixi.Payment.Field.ITEM_NAME] = data.item_name;
	params[mixi.Payment.Field.SIGNATURE] = data.signature;
	params[mixi.Payment.Field.ITEM_ID] = data.item_id;
	params[mixi.Payment.Field.IS_TEST] = data.is_test;
	params[mixi.Payment.Field.INVENTORY_CODE] = data.inventory_code
	params[opensocial.Payment.Field.PAYMENT_TYPE] = opensocial.Payment.PaymentType.PAYMENT;
	
	var payment = opensocial.newPayment(params);
	
	opensocial.requestPayment(payment, data.callback_url, function(response) {
		
		var code = 200;
		var ret = true;
		
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
