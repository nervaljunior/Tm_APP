/*LIB*/
!function(t,e){"object"==typeof exports&&"undefined"!=typeof module?module.exports=e():"function"==typeof define&&define.amd?define(e):t.SignaturePad=e()}(this,function(){"use strict";function t(t,e,i){this.x=t,this.y=e,this.time=i||(new Date).getTime()}function e(t,e,i,o){this.startPoint=t,this.control1=e,this.control2=i,this.endPoint=o}function i(t,e,i){var o,n,s,r=null,h=0;i||(i={});var a=function(){h=!1===i.leading?0:Date.now(),r=null,s=t.apply(o,n),r||(o=n=null)};return function(){var c=Date.now();h||!1!==i.leading||(h=c);var d=e-(c-h);return o=this,n=arguments,d<=0||d>e?(r&&(clearTimeout(r),r=null),h=c,s=t.apply(o,n),r||(o=n=null)):r||!1===i.trailing||(r=setTimeout(a,d)),s}}function o(t,e){var n=this,s=e||{};this.velocityFilterWeight=s.velocityFilterWeight||.7,this.minWidth=s.minWidth||.5,this.maxWidth=s.maxWidth||2.5,this.throttle="throttle"in s?s.throttle:16,this.minDistance="minDistance"in s?s.minDistance:5,this.throttle?this._strokeMoveUpdate=i(o.prototype._strokeUpdate,this.throttle):this._strokeMoveUpdate=o.prototype._strokeUpdate,this.dotSize=s.dotSize||function(){return(this.minWidth+this.maxWidth)/2},this.penColor=s.penColor||"black",this.backgroundColor=s.backgroundColor||"rgba(0,0,0,0)",this.onBegin=s.onBegin,this.onEnd=s.onEnd,this._canvas=t,this._ctx=t.getContext("2d"),this.clear(),this._handleMouseDown=function(t){1===t.which&&(n._mouseButtonDown=!0,n._strokeBegin(t))},this._handleMouseMove=function(t){n._mouseButtonDown&&n._strokeMoveUpdate(t)},this._handleMouseUp=function(t){1===t.which&&n._mouseButtonDown&&(n._mouseButtonDown=!1,n._strokeEnd(t))},this._handleTouchStart=function(t){if(1===t.targetTouches.length){var e=t.changedTouches[0];n._strokeBegin(e)}},this._handleTouchMove=function(t){t.preventDefault();var e=t.targetTouches[0];n._strokeMoveUpdate(e)},this._handleTouchEnd=function(t){t.target===n._canvas&&(t.preventDefault(),n._strokeEnd(t))},this.on()}return t.prototype.velocityFrom=function(t){return this.time!==t.time?this.distanceTo(t)/(this.time-t.time):1},t.prototype.distanceTo=function(t){return Math.sqrt(Math.pow(this.x-t.x,2)+Math.pow(this.y-t.y,2))},t.prototype.equals=function(t){return this.x===t.x&&this.y===t.y&&this.time===t.time},e.prototype.length=function(){for(var t=0,e=void 0,i=void 0,o=0;o<=10;o+=1){var n=o/10,s=this._point(n,this.startPoint.x,this.control1.x,this.control2.x,this.endPoint.x),r=this._point(n,this.startPoint.y,this.control1.y,this.control2.y,this.endPoint.y);if(o>0){var h=s-e,a=r-i;t+=Math.sqrt(h*h+a*a)}e=s,i=r}return t},e.prototype._point=function(t,e,i,o,n){return e*(1-t)*(1-t)*(1-t)+3*i*(1-t)*(1-t)*t+3*o*(1-t)*t*t+n*t*t*t},o.prototype.clear=function(){var t=this._ctx,e=this._canvas;t.fillStyle=this.backgroundColor,t.clearRect(0,0,e.width,e.height),t.fillRect(0,0,e.width,e.height),this._data=[],this._reset(),this._isEmpty=!0},o.prototype.fromDataURL=function(t){var e=this,i=arguments.length>1&&void 0!==arguments[1]?arguments[1]:{},o=new Image,n=i.ratio||window.devicePixelRatio||1,s=i.width||this._canvas.width/n,r=i.height||this._canvas.height/n;this._reset(),o.src=t,o.onload=function(){e._ctx.drawImage(o,0,0,s,r)},this._isEmpty=!1},o.prototype.toDataURL=function(t){var e;switch(t){case"image/svg+xml":return this._toSVG();default:for(var i=arguments.length,o=Array(i>1?i-1:0),n=1;n<i;n++)o[n-1]=arguments[n];return(e=this._canvas).toDataURL.apply(e,[t].concat(o))}},o.prototype.on=function(){this._handleMouseEvents(),this._handleTouchEvents()},o.prototype.off=function(){this._canvas.removeEventListener("mousedown",this._handleMouseDown),this._canvas.removeEventListener("mousemove",this._handleMouseMove),document.removeEventListener("mouseup",this._handleMouseUp),this._canvas.removeEventListener("touchstart",this._handleTouchStart),this._canvas.removeEventListener("touchmove",this._handleTouchMove),this._canvas.removeEventListener("touchend",this._handleTouchEnd)},o.prototype.isEmpty=function(){return this._isEmpty},o.prototype._strokeBegin=function(t){this._data.push([]),this._reset(),this._strokeUpdate(t),"function"==typeof this.onBegin&&this.onBegin(t)},o.prototype._strokeUpdate=function(t){var e=t.clientX,i=t.clientY,o=this._createPoint(e,i),n=this._data[this._data.length-1],s=n&&n[n.length-1],r=s&&o.distanceTo(s)<this.minDistance;if(!s||!r){var h=this._addPoint(o),a=h.curve,c=h.widths;a&&c&&this._drawCurve(a,c.start,c.end),this._data[this._data.length-1].push({x:o.x,y:o.y,time:o.time,color:this.penColor})}},o.prototype._strokeEnd=function(t){var e=this.points.length>2,i=this.points[0];if(!e&&i&&this._drawDot(i),i){var o=this._data[this._data.length-1],n=o[o.length-1];i.equals(n)||o.push({x:i.x,y:i.y,time:i.time,color:this.penColor})}"function"==typeof this.onEnd&&this.onEnd(t)},o.prototype._handleMouseEvents=function(){this._mouseButtonDown=!1,this._canvas.addEventListener("mousedown",this._handleMouseDown),this._canvas.addEventListener("mousemove",this._handleMouseMove),document.addEventListener("mouseup",this._handleMouseUp)},o.prototype._handleTouchEvents=function(){this._canvas.style.msTouchAction="none",this._canvas.style.touchAction="none",this._canvas.addEventListener("touchstart",this._handleTouchStart),this._canvas.addEventListener("touchmove",this._handleTouchMove),this._canvas.addEventListener("touchend",this._handleTouchEnd)},o.prototype._reset=function(){this.points=[],this._lastVelocity=0,this._lastWidth=(this.minWidth+this.maxWidth)/2,this._ctx.fillStyle=this.penColor},o.prototype._createPoint=function(e,i,o){var n=this._canvas.getBoundingClientRect();return new t(e-n.left,i-n.top,o||(new Date).getTime())},o.prototype._addPoint=function(t){var i=this.points,o=void 0;if(i.push(t),i.length>2){3===i.length&&i.unshift(i[0]),o=this._calculateCurveControlPoints(i[0],i[1],i[2]);var n=o.c2;o=this._calculateCurveControlPoints(i[1],i[2],i[3]);var s=o.c1,r=new e(i[1],n,s,i[2]),h=this._calculateCurveWidths(r);return i.shift(),{curve:r,widths:h}}return{}},o.prototype._calculateCurveControlPoints=function(e,i,o){var n=e.x-i.x,s=e.y-i.y,r=i.x-o.x,h=i.y-o.y,a={x:(e.x+i.x)/2,y:(e.y+i.y)/2},c={x:(i.x+o.x)/2,y:(i.y+o.y)/2},d=Math.sqrt(n*n+s*s),l=Math.sqrt(r*r+h*h),u=a.x-c.x,v=a.y-c.y,p=l/(d+l),_={x:c.x+u*p,y:c.y+v*p},y=i.x-_.x,f=i.y-_.y;return{c1:new t(a.x+y,a.y+f),c2:new t(c.x+y,c.y+f)}},o.prototype._calculateCurveWidths=function(t){var e=t.startPoint,i=t.endPoint,o={start:null,end:null},n=this.velocityFilterWeight*i.velocityFrom(e)+(1-this.velocityFilterWeight)*this._lastVelocity,s=this._strokeWidth(n);return o.start=this._lastWidth,o.end=s,this._lastVelocity=n,this._lastWidth=s,o},o.prototype._strokeWidth=function(t){return Math.max(this.maxWidth/(t+1),this.minWidth)},o.prototype._drawPoint=function(t,e,i){var o=this._ctx;o.moveTo(t,e),o.arc(t,e,i,0,2*Math.PI,!1),this._isEmpty=!1},o.prototype._drawCurve=function(t,e,i){var o=this._ctx,n=i-e,s=Math.floor(t.length());o.beginPath();for(var r=0;r<s;r+=1){var h=r/s,a=h*h,c=a*h,d=1-h,l=d*d,u=l*d,v=u*t.startPoint.x;v+=3*l*h*t.control1.x,v+=3*d*a*t.control2.x,v+=c*t.endPoint.x;var p=u*t.startPoint.y;p+=3*l*h*t.control1.y,p+=3*d*a*t.control2.y,p+=c*t.endPoint.y;var _=e+c*n;this._drawPoint(v,p,_)}o.closePath(),o.fill()},o.prototype._drawDot=function(t){var e=this._ctx,i="function"==typeof this.dotSize?this.dotSize():this.dotSize;e.beginPath(),this._drawPoint(t.x,t.y,i),e.closePath(),e.fill()},o.prototype._fromData=function(e,i,o){for(var n=0;n<e.length;n+=1){var s=e[n];if(s.length>1)for(var r=0;r<s.length;r+=1){var h=s[r],a=new t(h.x,h.y,h.time),c=h.color;if(0===r)this.penColor=c,this._reset(),this._addPoint(a);else if(r!==s.length-1){var d=this._addPoint(a),l=d.curve,u=d.widths;l&&u&&i(l,u,c)}}else{this._reset();o(s[0])}}},o.prototype._toSVG=function(){var t=this,e=this._data,i=this._canvas,o=Math.max(window.devicePixelRatio||1,1),n=i.width/o,s=i.height/o,r=document.createElementNS("http://www.w3.org/2000/svg","svg");r.setAttributeNS(null,"width",i.width),r.setAttributeNS(null,"height",i.height),this._fromData(e,function(t,e,i){var o=document.createElement("path");if(!(isNaN(t.control1.x)||isNaN(t.control1.y)||isNaN(t.control2.x)||isNaN(t.control2.y))){var n="M "+t.startPoint.x.toFixed(3)+","+t.startPoint.y.toFixed(3)+" C "+t.control1.x.toFixed(3)+","+t.control1.y.toFixed(3)+" "+t.control2.x.toFixed(3)+","+t.control2.y.toFixed(3)+" "+t.endPoint.x.toFixed(3)+","+t.endPoint.y.toFixed(3);o.setAttribute("d",n),o.setAttribute("stroke-width",(2.25*e.end).toFixed(3)),o.setAttribute("stroke",i),o.setAttribute("fill","none"),o.setAttribute("stroke-linecap","round"),r.appendChild(o)}},function(e){var i=document.createElement("circle"),o="function"==typeof t.dotSize?t.dotSize():t.dotSize;i.setAttribute("r",o),i.setAttribute("cx",e.x),i.setAttribute("cy",e.y),i.setAttribute("fill",e.color),r.appendChild(i)});var h='<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 '+n+" "+s+'" width="'+n+'" height="'+s+'">',a=r.innerHTML;if(void 0===a){var c=document.createElement("dummy"),d=r.childNodes;c.innerHTML="";for(var l=0;l<d.length;l+=1)c.appendChild(d[l].cloneNode(!0));a=c.innerHTML}var u=h+a+"</svg>";return"data:image/svg+xml;base64,"+btoa(u)},o.prototype.fromData=function(t){var e=this;this.clear(),this._fromData(t,function(t,i){return e._drawCurve(t,i.start,i.end)},function(t){return e._drawDot(t)}),this._data=t},o.prototype.toData=function(){return this._data},o});


/*COMPONENT*/
function BSignatureDrawCapture(id, value, penColor, service_action, filehandle) {
    this.canvas = document.getElementById(`canvas_${id}`);
    this.filehandle = filehandle;
    this.value = value;
    this.id = id;
    this.service_action = service_action;
    this.icon = $(`#${id}`).closest('.bsignaturedrawcapture_container').find('span');
    this.signaturePad = new SignaturePad(this.canvas, { backgroundColor: 'rgb(255, 255, 255)'});
    this.signaturePad.penColor = penColor;

    var that = this;

    this.start = function() {
        if (! that.value) {
            that.icon.show();
        } else {
            that.icon.hide();
        }

        $(`#${that.id}`).data('signature_pad', that.signaturePad);
    
        $(`#clear_${that.id}`).on('click', function() {
            that.signaturePad.clear();
        });
    
        $(`#add_${that.id}`).on('click', function() {
            if (that.signaturePad.isEmpty()) {
                that.icon.show();
                $(`#image_${that.id}`).css('background-image', '');
            } else {
                that.icon.hide();
                $(`#image_${that.id}`).css('background-image', `url(${that.signaturePad.toDataURL()})`);
            }
    
            that.setVal();
    
            $('#container_' + that.id).hide();
        });
        
        $(`#close_${that.id}`).on('click', function() {
            $('#container_' + that.id).hide();
        });
    
        $(`#image_${that.id},#container_${that.id} + input + .bsignaturedrawcapture_image + span`).on('click', function() {
            $('#container_' + that.id).show();
            that.resizeCanvas();
        });

        that.resizeCanvas();
    }

    this.setVal = function() {
        if (that.filehandle) {
            var data = that.getData();

            if (that.signaturePad.isEmpty()) {
                if (data.fileName && ! data.fileName.match(/tmp\/[0-9]*.png/)) {
                    data.delFile = data.fileName;
                }

                if (Object.values(data).length > 0) {
                    that.setData(encodeURIComponent(JSON.stringify(data)));
                }
            } else {
                var form_data = new FormData();
                
                var fileName = parseInt(Math.random() * 10000000000)+ '.png';
                
                fetch(that.signaturePad.toDataURL()).then(res => res.blob()).then(blob => {
                    const file = new File([blob], fileName,{ type: "image/png" })
                    form_data.append('fileName', file);
                    that.send(form_data, that.service_action);
                });
            }
        } else {
            if (that.signaturePad.isEmpty()) {
                that.setData('');
            } else {
                that.setData(that.signaturePad.toDataURL());
            }
        }
    }

    this.getData = function()
    {
        if (! $(`#${that.id}`).val()) {
            return {};
        }

        var file_data = decodeURIComponent($(`#${that.id}`).val());
        var file_data_json = '';
        
        try {
            file_data_json = file_data ? JSON.parse(file_data) : {};
        }
        catch(e) {
            file_data_json = {};
        }
        return file_data_json;
    }

    this.setData = function(value) {
        $(`#${that.id}`).val(value).blur();
    }

    this.send = function(form_data, uri) {
        var xhr = new XMLHttpRequest();
        xhr.addEventListener('readystatechange', that.onReadyStateChange, false);
        xhr.open('POST', uri, true);
        xhr.send(form_data);
    }

    this.onReadyStateChange = function(evt)
    {
        var status = null;
        
        try {
            status = evt.target.status;
        }
        catch(e) {
            return;
        }
        
        if (status == '200' && evt.target.readyState == '4' && evt.target.responseText) {
            try {
                var response = JSON.parse( evt.target.responseText );
                
                if ( response.type == 'success' ) {
                    var file_data = that.getData();
                    
                    // if there was already a file, then tell server to delete it
                    if (file_data.fileName && ! file_data.fileName.match(/tmp\/[0-9]*.png/)) {
                        file_data.delFile = file_data.fileName;
                    }
                    
                    file_data.newFile  = 'tmp/' + response.fileName;
                    file_data.fileName = 'tmp/' + response.fileName;

                    that.setData(encodeURIComponent(JSON.stringify(file_data)));
                }
                else {
                    __adianti_error('Error', e.message);
                }
            }
            catch (e) {
                __adianti_error('Error', e.message);
            }
        }
    };

    this.resizeCanvas = function() {
        var ratio =  Math.max(window.devicePixelRatio || 1, 1);
        that.canvas.width = that.canvas.offsetWidth * ratio;
        that.canvas.height = that.canvas.offsetHeight * ratio;
        that.canvas.getContext("2d").scale(ratio, ratio);
        that.signaturePad.clear();

        if (that.filehandle && $(`#${id}`).val()) {
            var data = that.getData();
            if (data.fileName && data.fileName != data.delFile) {
                that.loadImage('download.php?file='+data.fileName, function(base64){
                    that.signaturePad.fromDataURL(base64);
                })
            }
        } else if ($(`#${id}`).val()) {
            that.signaturePad.fromDataURL($(`#${id}`).val());
        }
    }

    this.loadImage = function(url, callback) {
        var xhr = new XMLHttpRequest();
        xhr.onload = function() {
            var reader = new FileReader();
            reader.onloadend = function() {
                callback(reader.result);
            }
            reader.readAsDataURL(xhr.response);
        };
        xhr.open('GET', url);
        xhr.responseType = 'blob';
        xhr.send();
    }
}

function bsignaturedrawcapture_set_value(id, value)
{
    $(`#${id}`).val( value );
    if(value.indexOf('%7B%') >= 0 )
    {
        data = JSON.parse(decodeURIComponent(value));
        value = data.fileName
    }
    console.log($(`#image_${id}`), value);
    $(`#image_${id}`).css('background-image', `url('download.php?file=${value}')`);

    if (value) {
        $(`#${id}`).closest('.bsignaturedrawcapture_container').find('span').hide();
    } else {
        $(`#${id}`).closest('.bsignaturedrawcapture_container').find('span').show();
    }
}

function bsignaturedrawcapture_start(id, value, penColor, service_action, filehandle) {
    var bsignaturedrawcapture = new BSignatureDrawCapture(id, value, penColor, service_action, filehandle);
    bsignaturedrawcapture.start();
}