/*
 * jQuery.gdocViewer - Embed linked documents using Google Docs Viewer
 * Licensed under MIT license.
 * Date: 2011/01/16
 *
 * @author Jawish Hameed
 * @version 1.0
 */
(
function(a){
	a.fn.gdocsViewer=function(b){var c={width:"900",height:"500"};
if(b){a.extend(c,b)}return this.each(function(){var d=a(this).attr("href");var e=d.substring(d.lastIndexOf(".")+1);if(/^(doc|docx|xls|xlsx|ppt|pptx|pdf|psd|tiff|ttf|pps|txt|zip|rar|png|jpg|jpeg|gif)$/.test(e)){a(this).after(function(){var g=a(this).attr("id");var f=(typeof g!=="undefined"&&g!==false)?g+"-gdocsviewer":"";return'<div id="'+f+'" class="gdocsviewer"><iframe src="https://docs.google.com/viewer?embedded=true&url='+encodeURIComponent(d)+'" width="'+c.width+'" height="'+c.height+'" style="border: none;"></iframe></div>'})}})}})(jQuery);