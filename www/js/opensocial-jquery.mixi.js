/*!
 * OpenSocial jQuery mixi Platform 1.0.0
 * http://code.google.com/p/opensocial-jquery/
 *
 * Copyright(C) 2009 Nakajiman Software Inc.
 * http://nakajiman.lrlab.to/
 *
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 */
(function() { 

/*
 * Container Selector
 */
jQuery.extend(jQuery.container, {
  mixi: true, sandbox: false
});

jQuery('html').addClass('mixi');

jQuery.extend({

// Keep a copy of the old view
_view: jQuery.view,

/*
 * Change View
 */
view: function(name, data) {
  
  var re = /^(?:\w+:)?\/\/([^\/?#]+)/;
  if (!re.test(name || ''))
    return jQuery._view(name, data);
  var host = re.exec(name)[1];

  data = jQuery.param(data || {});
  if (data)
    name += (name.match(/\?/) ? '&' : '?') + data;
  
  if (/\.mixi(\.co)?\.jp$/.test('.'+host))
    window.open(name, '_top'); // .mixi.jp and .mixi.co.jp
  else
    mixi.util.requestExternalNavigateTo(name);
},

/*
 * Invite Friends
 */ 
invite: function(groupId, fn) {
  var deferred = $.deferred();

  if (jQuery.isFunction(groupId)) {
    fn = groupId; groupId = null;
  }
  
  var callback = function(res) {
    if (res.hadError()) {
      setTimeout(function() { // Delay
        deferred.fail(res.getErrorCode());
	  }, 0);
	} else {
	  var recipientIds = res.getData()['recipientIds'];
	  if (fn)
		fn(recipientIds);
	  deferred.call(recipientIds);
	}
  };
  
  if (groupId == '@classmates')
    mixi.classmates.requestShareApp(callback)
  else // @friends
    opensocial.requestShareApp('VIEWER_FRIENDS', null, callback);
  
  return deferred;
}

});

/*
 * XHR Helper
 */
var status = {
  ok: 200,
  notImplemented: 501,
  unauthorized: 401,
  forbidden: 403,
  badRequest: 400,
  internalError: 500,
  limitExceeded: 417
};

var factory = jQuery.ajaxSettings.xhr;

/*
 * XHR Get Communities
 */ 
var xhr = function() {
  this.initialize();
};

jQuery.extend(xhr.prototype, jQuery._xhr.prototype, {
  send: function() {

    var self = this, url = self.url, selector = {
      '@me': 'VIEWER', '@viewer': 'VIEWER', '@owner': 'OWNER'
    };

    var userId = url.split('/')[2];
	if (selector[userId])
	  userId = selector[userId];
	
    var req = opensocial.newDataRequest();
    req.add(mixi.newFetchCommunityRequest(userId), 'data');
    req.send(function(res) {
      self.readyState = 4; // DONE
	  
	  // failed
	  if (res.hadError()) {

        var item = res.get('data') ||
		  new opensocial.ResponseItem(null, null, 'internalError');
		self.status = status[item.getErrorCode()];
        self.statusText = item.getErrorCode();
	  
	  // succeeded
	  } else {
		
		var item = res.get('data');
        var collection = item.getData();
		
        var communities = jQuery.map(collection.asArray(), function(community) {
		  var id = 	community.getField(mixi.Community.Field.ID).split('/');
		  var url = 'http://mixi.jp/view_community.pl?' + jQuery.param({ id: id[1] });
		  return {
			id: id[1], userId: id[0], url: url,
		    name: community.getField(mixi.Community.Field.TITLE),
			thumbnailUrl: community.getField(mixi.Community.Field.THUMBNAIL_URL)
		  };
		});
        
		//communities.startIndex = 0;
        //communities.itemsPerPage = 20;
        communities.totalResults = collection.getTotalSize();
		
		self.status = status.ok;
        self.statusText = 'ok';
        self.responseData = communities;
	  }
	});
  }
});

factory.addRoute('GET', '/communities/', xhr);

/*
 * XHR Get Schools
 */ 
var xhr = function() {
  this.initialize();
};

jQuery.extend(xhr.prototype, jQuery._xhr.prototype, {
  send: function() {

    var self = this, url = self.url, selector = {
      '@me': 'VIEWER', '@viewer': 'VIEWER'
    }, division = {
      '01': "\u5C0F\u5B66\u6821",
	  '02': "\u4E2D\u5B66\u6821",
	  '03': "\u9AD8\u7B49\u5B66\u6821",
	  '04': "\u5927\u5B66",
	  '05': "\u77ED\u671F\u5927\u5B66",
	  '06': "\u5927\u5B66\u9662",
	  '07': "\u5C02\u9580\u5B66\u6821\u30FB\u4E88\u5099\u6821",
	  '08': "\u9AD8\u7B49\u5C02\u9580\u5B66\u6821",
	  '09': "\u4E2D\u7B49\u6559\u80B2\u5B66\u6821",
	  '10': "\u305D\u306E\u4ED6\u306E\u6559\u80B2\u6A5F\u95A2"
    };

    var userId = url.split('/')[2];
	if (selector[userId])
	  userId = selector[userId];

    // @selected
	if (userId == '@selected') {
      
	  mixi.classmates.requestFetchSchool(mixi.classmates.SchoolSelectType.LIST, function(res) {
        self.readyState = 4; // DONE

	    // failed
	    if (res.hadError()) {

		  self.status = status[res.getErrorCode()];
          self.statusText = res.getErrorCode();

		// succeeded
	    } else {
     
          var school = res.getData().school;
		  var schools = [{
		    token: school.getField(mixi.classmates.SchoolField.TOKEN),
		    divisionId: school.getField(mixi.classmates.SchoolField.DIVISION),
		    division: division[school.getField(mixi.classmates.SchoolField.DIVISION)]
		  }];

		  //schools.startIndex = 0;
          //schools.itemsPerPage = 20;
          schools.totalResults = schools.length;

		  self.status = status.ok;
          self.statusText = 'ok';
          self.responseData = schools;
		}
      });
	
    // Others
    } else {
      
	  var req = opensocial.newDataRequest();
      req.add(mixi.classmates.newFetchSchoolsRequest(userId), 'data');
      req.send(function(res) {
        self.readyState = 4; // DONE

	    // failed
	    if (res.hadError()) {

          var item = res.get('data') ||
		    new opensocial.ResponseItem(null, null, 'internalError');
		  self.status = status[item.getErrorCode()];
          self.statusText = item.getErrorCode();
	  
	    // succeeded
	    } else {

		  var item = res.get('data');
          var collection = item.getData();

          var schools = jQuery.map(collection.asArray(), function(school) {
		    return {
		      token: school.getField(mixi.classmates.SchoolField.TOKEN),
		      divisionId: school.getField(mixi.classmates.SchoolField.DIVISION),
		      division: division[school.getField(mixi.classmates.SchoolField.DIVISION)]
		    };
		  });

		  //schools.startIndex = 0;
          //schools.itemsPerPage = 20;
          schools.totalResults = collection.getTotalSize();

          self.status = status.ok;
          self.statusText = 'ok';
          self.responseData = schools;
		}
	  });
    
	}
  }
});

factory.addRoute('GET', '/schools/', xhr);

})();