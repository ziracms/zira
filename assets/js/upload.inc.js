UploadAPI = function(uri) {
	this.uri=null;
	this.fallback_frame_id='upload-api-fallback-frame';
	
	if (typeof(uri)!='undefined') 
		this.uri=uri;
};

UploadAPI.prototype.init=function(form) {
	if (!this.uri && form.action.length>0) 
		this.uri=form.action;
	else if (!this.uri) 
		this.uri=window.location.href;
		
	try {
		this.initLevel2(form);
	} catch(err) {
		this.initFallback(form);
	}
};

UploadAPI.prototype.initLevel2=function(form) {
	var formData=new FormData(form);
	var xhr = new XMLHttpRequest();
	xhr.open(form.method,this.uri);
	//xhr.setRequestHeader("Content-type", "multipart/form-data"); 
	xhr.onreadystatechange=this.bind(this.handleState,this,xhr,form);
	xhr.upload.onprogress=this.bind(this.handleProgress,this);
	
	xhr.send(formData);
	this.disableForm(form);
};

UploadAPI.prototype.initFallback=function(form) {
	var frame=null;
	try {
		frame=document.createElement('<iframe name="'+this.fallback_frame_id+'" />');
	} catch(err) {
		frame=document.createElement('iframe');
		frame.name=this.fallback_frame_id;
	}
	
	if (!frame) return;
	
	frame.id=this.fallback_frame_id;
	//frame.src=window.location.href;
	
	frame.width=1;
	frame.height=1;
	frame.style.position='absolute';
	frame.style.right='0px';
	frame.style.bottom='0px';
	frame.style.visibility='hidden';
	
	document.body.appendChild(frame);
	
	var iframe=document.getElementById(this.fallback_frame_id);
	
	if (iframe.attachEvent)
		iframe.attachEvent('onload',this.bind(this.handleFallbackSuccess,this,form),false);
	else if(iframe.addEventListener)
		iframe.addEventListener('load',this.bind(this.handleFallbackSuccess,this,form),false);
	else
		iframe.onload=this.bind(this.handleFallbackSuccess,this,form);
	
	this.handleProgress({lengthComputable:1,loaded:10,total:100});
	
	form.action=this.uri;
	form.target=this.fallback_frame_id;
	
	form.submit();
	this.disableForm(form);
};

UploadAPI.prototype.handleFallbackSuccess=function(e,form) {
	this.enableForm(form);
	var iframe=document.getElementById(this.fallback_frame_id);
	
	var contentDocument=null;
	if (iframe.contentDocument) contentDocument=iframe.contentDocument;
	else contentDocument=document.frames[this.fallback_frame_id].document;
	
	var responseText='';
	
	if (contentDocument) {
		responseText=contentDocument.body.innerHTML;
	}
	
	document.body.removeChild(iframe);
	this.onSuccess.call(this,responseText,true);
};


UploadAPI.prototype.handleState=function(e,xhr,form) {
	if (xhr.readyState==4 && xhr.status==200) {
		this.enableForm(form);
		this.onSuccess.call(this,xhr.responseText);
	} else if (xhr.readyState==4 && xhr.status!=200) {
		this.enableForm(form);
		this.onError.call(this,xhr.responseText,xhr.status);
	}
};

UploadAPI.prototype.handleProgress=function(e) {
	if (e.lengthComputable) {
		var percentComplete = Math.round(e.loaded / e.total * 100);
		this.onProgress.call(this,percentComplete,e.loaded,e.total);
	}
};

UploadAPI.prototype.bind=function(method,object,param1,param2) {
	return function(event) {
		return method.call(object,event,param1,param2);
	}
};

UploadAPI.prototype.disableForm=function(form) {
    var co = document.forms[0].elements.length;
    for (var i=0;i<co;i++) {
      document.forms[0].elements[i].disabled = true;
    }
};

UploadAPI.prototype.enableForm=function(form) {
    var co = document.forms[0].elements.length;
    for (var i=0;i<co;i++) {
      document.forms[0].elements[i].disabled = false;
    }
};

UploadAPI.prototype.onSuccess=function(text,ignore_empty) {
	if (typeof(ignore_empty)!='undefined' && ignore_empty && !text) return; // used in fallback mode
	// should be reassigned
};

UploadAPI.prototype.onError=function(text,status) {
	// should be reassigned
};

UploadAPI.prototype.onProgress=function(percent,loaded,total) {
	// should be reassigned
};
